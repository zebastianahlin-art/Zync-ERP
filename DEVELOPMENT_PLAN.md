# ZYNC ERP — Utvecklingsplan & Roadmap

## Datum: 2026-03-04
## Status: Aktiv utveckling

---

## Bakgrund

ZYNC ERP ska bli ett komplett, modulärt industri-ERP som konkurrerar med SAP, IFS m.fl.
En detaljerad åtgärdslista har tagits fram för att uppgradera alla moduler till professionell nivå.

### Vad som redan är gjort (mergat i main):
- **PR #34** — Fixade döda menylänkar (departments, employees, certificates)
- **PR #35** — Fixade 5 döda menylänkar (inventory, my-page m.fl.)
- **PR #37** — 12 nya moduler (Production, Sales, Projects, HR-svit, Reports, i18n, Admin Roles, Equipment Docs, Spare Parts) + 9 migrationer (0020–0028)
- **PR #38** — Fas 1: Menystruktur + Routing-cleanup (PlaceholderController, ny menystruktur)
- **PR #39** — Fas 2: ObjektNavigator + Förebyggande Underhåll + AI-ingenjör (objektträd, FU-planerare, AI-analyser)
- **PR #40** — Fas 3: Lager + Inköp (lagerställen, transaktioner, inventering, leverantörsaudit, avtalsmallar)
- **PR #41** — Fas 4: Ekonomi (budgetar, anläggningstillgångar, kontoplansgrupper, balansräkning, kreditnotor)
- **PR #42** — Fas 5: Produktion + Försäljning + CS & Transport (produkter CRUD, ordrar, offertmallar, prislistor, ticketsystem, transportordrar)
- **PR #43** — Fas 6: H&S + Projekt + HR (pending/completed audits, nödlägesövningar med mallar, reseräkningar med rader, projekt tasks + budget CRUD)
- **PR #45** — Fas 7: Dashboard widget-system + Rapportmodul (widget-grid, KPI per modul, rapportgenerator, historik-routes, alla PlaceholderController-routes ersatta)

### Kända problem:
- Admin-sidan oförändrad, SaaS-admin saknas helt (planerat i Fas 8)

### Teknisk stack:
- PHP 8.4, Slim Framework, Tailwind CSS (CDN), AlpineJS, MariaDB
- Controllers i `zync-erp/app/Controllers/` (ärver Controller base class)
- Repository-klasser i `zync-erp/app/Models/` (PDO prepared statements)
- Views i `zync-erp/views/` (View::render() med main.php layout)
- Routes i `zync-erp/config/routes.php` (Slim, AuthMiddleware + CsrfMiddleware)
- Migrations i `zync-erp/database/migrations/` (NNNN_create_{table}_table.php)

---

## Genomförandeplan — 8 Faser

### Fas 1: Menystruktur + Routing-cleanup
**Status:** ✅ Klar (PR #38)

Bygga om hela main.php-menyn enligt ny struktur. Ta bort allt som inte ska finnas.
Verifiera att varje menyval har fungerande route → controller → view.
Skapa placeholder-sidor för moduler som ännu inte är uppgraderade.

**Ny menystruktur:**
1. Dashboard (konfigurerbar med widgets/KPI)
2. Underhåll (byt namn från "Drift")
3. ObjektNavigator (objektträd, maskiner, utrustning)
4. Lager (förråd/warehouse)
5. Inköp
6. Ekonomi
7. Health & Safety
8. Produktion
9. Försäljning
10. CS & Transport
11. Projekt
12. HR
13. Rapporter
14. Admin
15. SaaS Admin (separat)

### Fas 2: ObjektNavigator + Underhåll (uppgradering)
**Status:** ✅ Klar (PR #39)

- Objektträd med hierarki (Site → Avdelning → Maskin → Delar)
- Felanmälan → Arbetsorder-flöde
- Avrapportering med tid/material
- Förebyggande underhåll (FU-planerare, FU-rondering)
- AI-ingenjör (analyserar felanmälningsmönster)
- Historiska arbetsordrar
- Dashboard med KPI

### Fas 3: Lager + Inköp (uppgradering)
**Status:** ✅ Klar (PR #40)

**Lager:**
- Lagerartiklar CRUD (förbrukningsartikel, reservdel)
- Lagertransaktionshistorik
- Beställ lagerartiklar (lågt saldo-varning → inköpsanmodan)
- Inleverans (kopplat till inköpsorder)
- Uttag (bokning mot arbetsorder/kostnadsställe)
- Inventering (kopplat till ekonomi)

**Inköp:**
- Leverantörsregister + audit
- Inköpsanmodan (skapa, lista, historik)
- Inköpsorder (skapa, aktiva, historik)
- Avtal + avtalsmallar (varning vid utgångsdatum)

### Fas 4: Ekonomi (uppgradering)
**Status:** ✅ Klar (PR #41)

- Leverantörsfakturor (e-post/e-faktura mottagning, automatisk matchning)
- Skapa kundfaktura (produkter från lager, fritext)
- Kontoplan (svensk standard, redigerbar av CFO)
- Kostnadsställen
- KPI från avdelningar
- Huvudbok (svensk standard)
- Resultaträkning
- Inventering (kopplat till lager)

### Fas 5: Produktion + Försäljning + CS & Transport
**Status:** ✅ Klar (PR #42)

**Produktion:**
- Produktionslinjer (kopplat till ObjektNavigator)
- Produktionsplanering
- Skapa produkt (datablad, sammansättning, vikt, ID)
- Produktionslager (lagerplatser, kapacitet, interna flyttar)

**Försäljning:**
- Kundregister (kopplat till ekonomi)
- Offerter (skapa, mallar, aktiva, historik, accepterade)
- Fakturering (kopplat till ekonomi)
- Prislistor (per produkt/kund)

**CS & Transport:**
- Customer Service-moduler
- Transporthantering (internt + kontrakterade åkerier)

### Fas 6: H&S + Projekt + HR (uppgradering)
**Status:** ✅ Klar (PR #43)

**Health & Safety:**
- Riskhantering (rapportera + hantera, obligatorisk dashboard-knapp)
- Safety audits (mallar, åtgärdslistor, tilldelning)
- Krishantering (aktiv plan, obligatorisk dashboard-widget)
- Nödlägesövningar (mallar, schemaläggning)
- Nödresurser (brandsläckare, hjärtstartare, besiktningsintervall)

**Projekt:**
- Flytta befintliga projektmoduler, uppgradera till fullständig projekthantering

**HR:**
- Flytta befintliga HR-moduler, uppgradera
- Reseräkningar (+ knapp på MIN SIDA)

### Fas 7: Dashboard widget-system + Rapporter
**Status:** ✅ Klar (PR #45)

**Dashboard:**
- Konfigurerbar per användare (lägg till/ta bort widgets)
- Rollbaserade widgets (synlighet beror på användarroll)
- KPI-boxar kopplade till ALLA moduler
- Snabbknappar
- Obligatoriska widgets: Rapportera risk/fara, Krishanteringsplan

**Rapporter:**
- Dashboard med rapportgenerator
- Rapporter per modul
- Rollbaserad synlighet

### Fas 8: Admin + SaaS Admin
**Status:** ✅ Klar (PR #46)

**Admin (ERP):**
- ✅ Alla systeminställningar (system_settings med kategorier)
- ✅ Moduladministration (erp_modules med toggle)
- ✅ Site-inställningar (company_name, SMTP, tidszon, valuta m.m.)
- ✅ Uppgraderad Admin-dashboard med systeminformation och audit-logg
- ✅ Audit-logg med paginering och filter
- ✅ Migrationer: 0045 (admin-tabeller), 0046 (SaaS-tabeller)

**SaaS Admin (separat system):**
- ✅ Aktivera nya företagskunder (tenants CRUD)
- ✅ Företagsinställningar per kund (kontaktinfo, plan, status)
- ✅ Modulaktivering per abonnemang (saas_tenant_modules)
- ✅ Fakturering av kunder (saas_invoices med statushantering)
- ✅ Support-hantering (saas_support_tickets + kommentarer)
- ✅ SaaS Admin dashboard med KPIs

---

## Regler för alla PR:ar

1. ✅ ALLA routes dubbelkollas (controller → view → route → meny)
2. ✅ Inga döda länkar tillåts
3. ✅ Svensk text genomgående
4. ✅ Dark mode på alla element
5. ✅ CSRF-skydd på alla formulär
6. ✅ XSS-skydd (htmlspecialchars) på all output
7. ✅ Följ befintligt arkitekturmönster
8. ✅ Migrationer numreras sekventiellt efter senaste
9. ✅ Testa att inga 404:or uppstår efter merge

---

## PR-historik

| PR | Titel | Status |
|----|-------|--------|
| #34 | Fix dead menu links | ✅ Mergad |
| #35 | Fix 5 dead menu links | ✅ Mergad |
| #37 | Add 12 missing ERP modules | ✅ Mergad |
| #38 | Fas 1: Menystruktur + Routing-cleanup | ✅ Mergad |
| #39 | Fas 2: ObjektNavigator + Förebyggande Underhåll + AI-ingenjör | ✅ Mergad |
| #40 | Fas 3: Lager + Inköp — Full upgrade | ✅ Mergad |
| #41 | Fas 4: Ekonomi — Finance Module Upgrade | ✅ Mergad |
| #42 | Fas 5: Produktion + Försäljning + CS & Transport | ✅ Mergad |
| #43 | Fas 6: H&S + Projekt + HR — Fullständig uppgradering | ✅ Mergad |
| #45 | Fas 7: Dashboard widget-system + Rapportmodul | ✅ Mergad |
| #46 | Fas 8: Admin + SaaS Admin — Komplett uppgradering | ✅ Klar |
| #36 | (Ersatt av #37) | ❌ Stängd |
| #33 | Maintenance Module draft | ❌ Stängd |

---

## Kvarvarande PlaceholderController-routes

✅ **Alla PlaceholderController-routes är nu ersatta.** Fas 7 (PR #45) ersatte de sista 5 routerna:
- `/purchasing/requisitions/history` → `PurchaseController::requisitionHistory`
- `/purchasing/orders/history` → `PurchaseController::orderHistory`
- `/purchasing/agreements/history` → `PurchaseController::agreementHistory`
- `/finance/reports/kpi` → `FinanceController::reportKpi`
- `/finance/reports/stocktaking` → `FinanceController::reportStocktaking`
---

## Projektets slutstatus

### Alla 8 faser klara ✅

| Fas | Titel | Status |
|-----|-------|--------|
| Fas 1 | Menystruktur + Routing-cleanup | ✅ Klar |
| Fas 2 | ObjektNavigator + Förebyggande Underhåll + AI-ingenjör | ✅ Klar |
| Fas 3 | Lager + Inköp | ✅ Klar |
| Fas 4 | Ekonomi | ✅ Klar |
| Fas 5 | Produktion + Försäljning + CS & Transport | ✅ Klar |
| Fas 6 | H&S + Projekt + HR | ✅ Klar |
| Fas 7 | Dashboard widget-system + Rapportmodul | ✅ Klar |
| Fas 8 | Admin + SaaS Admin | ✅ Klar |

### Alla PlaceholderController-routes har ersatts med riktiga implementationer. ✅

### Implementerade moduler (15 st):
Dashboard, Underhåll, ObjektNavigator, Lager, Inköp, Ekonomi, Hälsa & Säkerhet, Produktion, Försäljning, Customer Service, Transport, Projekt, HR, Rapporter, Admin

### Totalt:
- **8 faser** klara
- **46 migrationer** körda
- **15 ERP-moduler** implementerade
- **0 PlaceholderController-routes** kvar
- **Totalt antal PRs:** #34, #35, #37, #38, #39, #40, #41, #42, #43, #45, #46
