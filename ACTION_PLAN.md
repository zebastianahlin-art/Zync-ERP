# ZYNC ERP — Fullständig Åtgärdsplan

## 📌 FAS A: Kritiska buggfixar (alla errors)
**Prio: AKUT — Appen kraschar**
**Status: ✅ Klar (PR #47, #48, #49, #50)**

| # | Modul | Problem | Orsak (trolig) |
|---|-------|---------|----------------|
| A1 | ObjektNavigator | "Synkronisera objekt" → error | API-endpoint saknas eller DB-tabell |
| A2 | Lager | Lageröversikt → error | warehouses-tabell join eller null-hantering |
| A3 | Lager | Lagerställen → error | Saknar users-join för ansvarig |
| A4 | Lager | Ny transaktion → error | Form-data mapping mot DB-kolumner |
| A5 | Lager | Inleverans → error | PO-join saknar data |
| A6 | Lager | Nytt uttag → error | Samma som A4 |
| A7 | Lager | Ny inventering → error | Saknar warehouse-relation |
| A8 | Ekonomi | Resultaträkning → error | FinanceReportRepository SQL-fel |
| A9 | Underhåll | AI-ingenjör → error | OpenAI API-nyckel eller endpoint saknas |
| A10 | CS | Dashboard → error | Tabell support_tickets vs cs_tickets namnkonflikt |
| A11 | CS | Alla ärenden → error | Samma tabellkonflikt |
| A12 | CS | Nytt ärende → error | Samma + form-validering |
| A13 | Projekt | Klick på projekt → error | findProject() saknar joins |
| A14 | HR | Certifikat → error | Tabell/kolumner saknas |
| A15 | HR | Lön → error | Payroll-tabell/data saknas |
| A16 | HR | Utbildning → error | Training-tabell join-fel |
| A17 | HR | Rekrytering → error | Recruitment-tabell join-fel |

## 📌 FAS B: UX & Funktionsuppgraderingar
**Prio: HÖG — Grundläggande kvalitet**
**Status: 🔄 Pågår**

| # | Modul | Åtgärd |
|---|-------|--------|
| B1 | ObjektNavigator | Byt namn till "Anläggningsregister" eller "Objekthantering" |
| B2 | Sälj — Ny Kund | Översätt till svenska, fixa färgtema till indigo dark mode |
| B3 | Sälj — Ny Offert | Kraftig uppgradering: kundval, produktval, prisberäkning, rabatter, villkor, PDF, e-post, konvertera offert→order |
| B4 | Sälj — Prislistor | Produktkoppling, kundgruppsrabatter, datumstyrda priser, import/export CSV |
| B5 | Transport — Ny Transport | Välj produkt från artikelregistret, kund som mottagare, leverantör som avsändare, spårning, fraktberäkning |
| B6 | Transport — Ny Transportör | Synkronisera med Leverantörsregistret |

## 📌 FAS C: Projektmodulen — Professionell nivå
**Prio: HÖG**
**Status: ✅ Klar (PR #59)**

| # | Åtgärd |
|---|--------|
| C1 | Val av internt/externt projekt vid skapande |
| C2 | Intressepersoner/stakeholders |
| C3 | Koppling till Inköp |
| C4 | Projektrapporter — PDF |
| C5 | Gantt-liknande tidslinje eller Kanban-vy |
| C6 | Budget-tracking med faktisk vs planerad kostnad |

## 📌 FAS D: HR-modulen — Enterprise-nivå
**Prio: HÖG**
**Status: ⏳ Planerad**

| # | Åtgärd |
|---|--------|
| D1 | HR Dashboard |
| D2 | Ny anställd — komplett formulär |
| D3 | Certifikat — fixa, koppla utbildning, förfallodatum |
| D4 | Lönespecar — fixa, generera, koppla till anställd |
| D5 | Utbildning — fixa, kursregister, bokningar |
| D6 | Rekrytering — fixa, jobbannonsering, kandidathantering |

## 📌 FAS E: Min Sida — Personlig arbetsyta
**Prio: MEDEL-HÖG**
**Status: ⏳ Planerad**

| # | Åtgärd |
|---|--------|
| E1 | Kalender |
| E2 | KPI-boxar och snabbknappar |
| E3 | Personlig info från HR |
| E4 | Lönespecar — se sina egna |
| E5 | Anställningsavtal — se sitt eget |
| E6 | Mina ärenden |

## 📌 FAS F: SaaS Admin — Multi-tenant plattform
**Prio: MYCKET HÖG — Affärsmodellens kärna**
**Status: ⏳ Planerad**

| # | Åtgärd |
|---|--------|
| F1 | Separera SaaS Admin helt |
| F2 | Tenant provisioning |
| F3 | Modulval per kund |
| F4 | Abonnemangsplaner |
| F5 | Kundfakturering |
| F6 | Tenant-status |
| F7 | Support-system |
| F8 | Multi-tenant arkitektur |

## Prioriteringsordning

| Ordning | Fas | Varför |
|---------|-----|--------|
| 1 | FAS A | Appen måste fungera utan errors |
| 2 | FAS B | Grundläggande kvalitet och konsistens |
| 3 | FAS D | HR är en kärnmodul i industri-ERP |
| 4 | FAS C | Projekt är kritisk för enterprise-kunder |
| 5 | FAS E | Min Sida knyter ihop upplevelsen |
| 6 | FAS F | SaaS-arkitekturen — affärsmodellens fundament |
