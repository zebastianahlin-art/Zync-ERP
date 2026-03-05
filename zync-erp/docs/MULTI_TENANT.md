# Multi-Tenant Arkitektur — ZYNC ERP SaaS

## Översikt

ZYNC ERP är byggt som en multi-tenant SaaS-plattform där varje kund (tenant) delar samma kodbas och databasinfrastruktur men har sina egna isolerade data.

## Arkitekturmodell: Shared Database, Separate Schema (logisk)

Vi använder **shared database** med **tenant_id-kolumner** i alla tenant-specifika tabeller. Detta är det vanligaste och mest kostnadseffektiva upplägget för en startup SaaS.

```
┌─────────────────────────────────────────────────────┐
│                    Internet                          │
└──────────────────────┬──────────────────────────────┘
                       │
          ┌────────────▼────────────┐
          │   Nginx / Load Balancer │
          │  (subdomain routing)    │
          └────────────┬────────────┘
                       │
          ┌────────────▼────────────┐
          │    ZYNC ERP App         │
          │  (PHP 8.3 + Slim 4)     │
          │                         │
          │  ┌─────────────────┐    │
          │  │TenantMiddleware │    │  ← Identifierar tenant
          │  └────────┬────────┘    │    via subdomain/header
          │           │             │
          │  ┌────────▼────────┐    │
          │  │ TenantContext   │    │  ← Singleton med aktiv tenant
          │  └────────┬────────┘    │
          │           │             │
          │  ┌────────▼────────┐    │
          │  │  Controllers    │    │
          │  └────────┬────────┘    │
          │           │             │
          │  ┌────────▼────────┐    │
          │  │  Repositories   │    │  ← Filtrerar med tenant_id
          │  └─────────────────┘    │
          └────────────┬────────────┘
                       │
          ┌────────────▼────────────┐
          │   MySQL/MariaDB         │
          │   (Shared Database)     │
          └─────────────────────────┘
```

## Tenant-identifiering

En tenant identifieras via **två mekanismer** (i prioritetsordning):

### 1. X-Tenant-ID HTTP-header (API-anrop)
```http
GET /api/v1/me
X-Tenant-ID: 42
Authorization: Bearer <jwt-token>
```

### 2. Subdomain (webbläsare)
```
https://acme-ab.zync-erp.se/  →  tenant = acme-ab
https://bolaget.zync-erp.se/  →  tenant = bolaget
```

`TenantMiddleware` identifierar automatiskt tenant och sätter `TenantContext`.

## Nyckelkomponenter

### TenantContext (`app/Core/TenantContext.php`)
Singleton som håller aktiv tenant-information under hela requesten.

```php
// Hämta aktiv tenant
$tenant = TenantContext::getInstance()->getTenant();
$plan   = TenantContext::getInstance()->get('plan');

// Kontrollera modulbehörighet
if (TenantContext::getInstance()->isModuleEnabled('hr')) {
    // HR-modul är aktiverad för denna tenant
}
```

### TenantMiddleware (`app/Middleware/TenantMiddleware.php`)
PSR-15 middleware som läser subdomain/header och sätter TenantContext.

```php
// Registrera i routes.php för tenant-aware routes
$app->group('/tenant-area', function($group) {
    // ...
})->add(new TenantMiddleware());
```

### TenantAwareRepository (`app/Models/TenantAwareRepository.php`)
Basklass för repositories som automatiskt filtrerar queries med `tenant_id`.

```php
class MyRepository extends TenantAwareRepository {
    public function all(): array {
        [$where, $params] = $this->tenantWhere('tenant_id', ['is_deleted = 0'], []);
        $stmt = $this->pdo()->prepare("SELECT * FROM my_table $where ORDER BY id DESC");
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

## Databastabeller

### SaaS Admin-tabeller (globala — inte tenant-specifika)
| Tabell | Syfte |
|--------|-------|
| `saas_tenants` | Kundregister |
| `saas_plans` | Abonnemangsplaner |
| `saas_tenant_modules` | Aktiverade moduler per tenant |
| `saas_tenant_history` | Historiklogg per tenant |
| `saas_tenant_settings` | Konfiguration per tenant |
| `saas_invoices` | Fakturor per tenant |
| `saas_support_tickets` | Supportärenden per tenant |
| `saas_support_comments` | Kommentarer på supportärenden |

### Tenant-data (innehåller tenant_id)
Alla tabeller med verksamhetsdata innehåller en `tenant_id`-kolumn som kopplar posten till rätt kund.

## Abonnemangsplaner

| Plan | Pris/mån | Pris/år | Max användare | Lagring |
|------|----------|---------|---------------|---------|
| **Starter** | 999 kr | 9 990 kr | 10 | 10 GB |
| **Professional** | 2 499 kr | 24 990 kr | 50 | 50 GB |
| **Enterprise** | 4 999 kr | 49 990 kr | ∞ | 500 GB |

## API-endpoints

### Tenant Info (publik)
```
GET /api/tenant-info?tenant_id=42
GET /api/tenant-info
  X-Tenant-ID: 42

Response:
{
  "id": 42,
  "company_name": "ACME AB",
  "plan": "professional",
  "status": "active",
  "active_modules": ["maintenance", "hr", "finance", "projects"]
}
```

## Modulsystem

Varje tenant kan ha olika moduler aktiverade baserat på sin plan:

- `maintenance` — Underhåll & Felanmälan
- `equipment` — Anläggningsregister
- `hr` — Personal & Löner
- `finance` — Ekonomi & Fakturering
- `projects` — Projekthantering
- `purchasing` — Inköp & Lager
- `sales` — Försäljning
- `admin` — Administration
- `integrations` — API-integrationer
- `risk` — Riskhantering
- `emergency` — Nödlarmsystem

## Tenant Livscykel

```
Trial (30 dagar)
    │
    ├─→ Aktiv (betalande kund)
    │       │
    │       ├─→ Pausad (tillfälligt inaktiv)
    │       │       │
    │       │       └─→ Aktiv (återaktiverad)
    │       │
    │       └─→ Avslutad (soft-deleted)
    │
    └─→ Avslutad (trial löpte ut)
```

## Framtida skalning

### Schema-level separation (Om-och-när det behövs)
För enterprise-kunder med höga krav på dataisolation kan vi migrera till en
"schema per tenant"-modell:

```sql
-- Varje tenant får sitt eget schema:
CREATE SCHEMA tenant_42;
CREATE TABLE tenant_42.employees (...);
```

Detta kräver att `Database::pdo()` returnerar en anslutning med rätt `search_path`.

### Read replicas
Läs-intensiva operationer kan styras till en replica:
```php
$readPdo  = Database::readPdo();
$writePdo = Database::pdo();
```

### Caching per tenant
Redis-nycklar prefixas med `tenant:{id}:` för att undvika kollisioner:
```php
$key = "tenant:{$tenantId}:dashboard_stats";
```

## Säkerhetsaspekter

1. **Tenant isolation**: `tenant_id` filtreras alltid i WHERE-klausulen — aldrig uteslutet
2. **CSRF**: Alla formulär använder `App\Core\Csrf::field()`
3. **Input sanitering**: `htmlspecialchars()` överallt i vyer
4. **Role-based access**: SaaS Admin kräver `role_level >= 9`
5. **Subdomain validation**: Valideras mot databas — inga pattern-matching-attacker

## Utvecklingsguide

### Lägga till en tenant-aware tabell
1. Skapa migrationen med `tenant_id INT UNSIGNED NOT NULL`
2. Lägg till `FOREIGN KEY (tenant_id) REFERENCES saas_tenants(id)`
3. Extenda `TenantAwareRepository` i ditt repository
4. Använd `$this->tenantWhere()` för alla queries

### Lägga till en ny modul
1. Registrera slugen i `erp_modules`-tabellen
2. Lägg till i `saas_plans.included_modules` JSON för rätt plan(er)
3. Kontrollera modulbehörighet: `TenantContext::getInstance()->isModuleEnabled('min-modul')`
