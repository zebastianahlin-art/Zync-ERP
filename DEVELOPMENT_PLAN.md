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

### Kända problem:
- Dashboard är enkel — saknar widget-system (planerat i Fas 7)
- Admin-sidan oförändrad, SaaS-admin saknas helt (planerat i Fas 8)
- Kvarvarande PlaceholderController-routes tillhör Fas 6+:
  - /purchasing/requisitions/history, /purchasing/orders/history, /purchasing/agreements/history
  - /finance/reports/kpi, /finance/reports/stocktaking
  - /safety/audits/pending, /safety/audits/completed
  - /safety/emergency/drills, /safety/emergency/drills/create, /safety/emergency/drills/templates
  - /hr/expenses (Reseräkningar)

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
**Status:** ⏳ Nästa

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
**Status:** ⏳ Väntar

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
**Status:** ⏳ Väntar

**Admin (ERP):**
- Alla systeminställningar
- Moduladministration
- Site-inställningar
- Abonnemangsinformation (från SaaS)

**SaaS Admin (separat system):**
- Aktivera nya företagskunder
- Företagsinställningar per kund
- Modulaktivering per abonnemang
- Fakturering av kunder
- Support-hantering

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
| #36 | (Ersatt av #37) | ❌ Stängd |
| #33 | Maintenance Module draft | ❌ Stängd |

---

## Kvarvarande PlaceholderController-routes

Följande routes pekar fortfarande på `PlaceholderController::comingSoon` och ska ersättas i respektive fas:

### Fas 6 (H&S + Projekt + HR)
| Route | Modul | Beskrivning |
|-------|-------|-------------|
| `/safety/audits/pending` | H&S | Ej slutförda åtgärder |
| `/safety/audits/completed` | H&S | Slutförda åtgärder |
| `/safety/emergency/drills` | H&S | Nödlägesövningar lista |
| `/safety/emergency/drills/create` | H&S | Skapa nödlägesövning |
| `/safety/emergency/drills/templates` | H&S | Nödlägesövning mallar |
| `/hr/expenses` | HR | Reseräkningar |

### Framtida faser
| Route | Modul | Beskrivning |
|-------|-------|-------------|
| `/purchasing/requisitions/history` | Inköp | Historiska anmodan |
| `/purchasing/orders/history` | Inköp | Historiska inköpsordrar |
| `/purchasing/agreements/history` | Inköp | Historiska avtal |
| `/finance/reports/kpi` | Ekonomi | KPI från avdelningar |
| `/finance/reports/stocktaking` | Ekonomi | Inventering ekonomi |