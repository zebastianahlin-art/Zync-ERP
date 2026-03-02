# ZYNC ERP — Projektöversikt & Roadmap

> Senast uppdaterad: 2026-03-02

---

## Vision

Ett enkelt men skalbart, modulärt ERP-system med alla smarta funktioner som krävs i vardagen — på ETT ställe, i ETT system. Målgrupp: **stora industrier**. Single-tenant med förberedelse för multi-tenant SaaS.

---

## Teknisk stack

| Komponent | Val |
|-----------|-----|
| Backend | PHP 8.4, custom MVC (PSR-15 middleware) |
| Databas | MariaDB |
| Frontend | Tailwind CSS + Alpine.js |
| Server | Apache, mod_rewrite |
| Deploy | GitHub Actions → VPS |
| Språk | Svenska (default) + Engelska (ej implementerat) |

---

## Modulöversikt

### ✅ Klart & fungerar

| Modul | Status | Noteringar |
|-------|--------|------------|
| Auth (login/logout) | ✅ Klar | Session-baserat, CSRF, 2FA |
| Dashboard | ✅ Grund | Behöver widgets med riktig data per roll |
| Min Sida | ✅ Klar | Profil, lösenord, avatar, tema-växling |
| Kunder | ✅ CRUD | Ska kopplas till Sales + Ekonomi |
| Leverantörer | ✅ CRUD | Ska kopplas till Inköp + Ekonomi |
| Artiklar | ✅ CRUD | Bas för Lager — maskinkoppling finns |
| Avdelningar | ✅ CRUD | Tillhör HR-modulen |
| Personal | ✅ CRUD | Tillhör HR — behöver löner, närvaro |
| Certifikat | ✅ CRUD | Certifikattyper, koppla till personal, datum |
| Maskinhierarki | ✅ Klar | Trädvy, detaljer, reservdelar, dokument |
| Utrustning | ✅ CRUD | Reservdelar, dokument |
| Felanmälan | ✅ CRUD | Skapa, hantera, koppla till maskin |
| Arbetsorder | ✅ Grund | Skapa, tilldela, status, kommentarer, tid, material |
| Lager | ✅ Grund | Lagerplatser, saldo, transaktioner |
| Roller (DB) | ✅ Tabeller | Permissions-system i DB |
| Dark/Light mode | ✅ Klar | Växling från Min Sida |
| Responsiv design | ✅ Klar | Tailwind |

### 🔧 Delvis byggt — behöver kompletteras

| Modul | Vad finns | Vad saknas |
|-------|-----------|------------|
| Underhåll | Felanmälan, arbetsorder, utrustning | Besiktningspliktig utrustning (återkommande schema), AI-analys |
| HR | Personal, avdelningar, certifikat | Lönehantering, närvaro/frånvaro, rekrytering, utbildningar, roller UI |
| Lager | Artiklar, lagerplatser, transaktioner | Inventering, inleveranser, uttag mot arbetsorder |
| Min Sida | Profil, lösenord, avatar, tema | Kalender, lönespec-visning |
| Dashboard | Grundvy | Rollbaserade widgets, KPI:er |

### ❌ Saknas — nya moduler att bygga

| Prio | Modul | Beskrivning | Komplexitet |
|------|-------|-------------|-------------|
| 🔴 1 | **Inköp** | Inköpsanmodan (skapa, attestera, tilldela inköpare), inköpsorder (skapande → utskick till leverantör med bilagor), avtalshantering (AB04, ABT06, NL10 m.fl.), leverantörskoppling | Stor |
| 🔴 2 | **Ekonomi** | Översikt/dashboard, fakturahantering (in/utgående), bokföringsmodul, koppling kunder/leverantörer | Stor |
| 🔴 3 | **Produktion** | Produktionslinjer, planering, produktionslager (råmaterial/färdigmaterial), koppling mot maskinhierarki | Stor |
| 🔴 4 | **Sales** | Kundhantering (utökad), offerter, orderingångar, prissättning, koppling mot produktion | Stor |
| 🟡 5 | **H/S (Hälsa & Säkerhet)** | Risker & faror (riskbedömningar), audits (mallar, tilldelning), krishantering (kontakter, rutiner), nödresurser (brandsläckare, hjärtstartare — lista/karta, kontrolluppföljning) | Medel |
| 🟡 6 | **HR — Närvaro/Frånvaro** | Semester, sjukfrånvaro, planering | Medel |
| 🟡 7 | **HR — Utbildningar** | Planering, koppling till certifikat, historik | Liten |
| 🟡 8 | **HR — Lönehantering** | Lönespec, koppling till Min Sida | Stor |
| 🟡 9 | **Projekt management** | Skapa projekt, tidsplaner, budgetar, historik | Medel |
| 🟡 10 | **HR — Rekrytering** | Mallar, strukturer | Medel |
| 🟢 11 | **Rapportmodul** | PDF-generering, statistik, delning mellan användare (gemensam för alla moduler) | Medel |
| 🟢 12 | **Språkstöd (i18n)** | Svenska (default) + Engelska, översättningsfiler | Medel |
| 🟢 13 | **AI-modul** | Analysera arbetsordrar, feltyper, föreslå åtgärder/inspektioner | Stor |
| 🟢 14 | **SaaS Backend** | Multi-tenant förberedelse, kundhantering, domäner, abonnemang, kontrakt, felanmälan, fakturering | Stor |

---

## Kritiska kopplingar (arkitektur)

Maskinhierarki ──┬── Underhåll (felanmälan, arbetsorder) ├── Produktion (linjer, planering) └── Lager (reservdelar kopplade till maskiner)

Leverantörer ────┬── Inköp (anmodan, ordrar, avtal) └── Ekonomi (fakturor)

Kunder ──────────┬── Sales (offerter, ordrar) └── Ekonomi (fakturor)

Personal ────────┬── HR (löner, närvaro, certifikat, utbildningar) ├── Underhåll (tilldelning arbetsorder) └── Min Sida (personuppgifter, kalender, lönespec)

Arbetsorder ─────┬── Lager (uttag reservdelar) ├── Tid (tidrapportering) └── AI (analys av feltyper)

Code

---

## Icke-funktionella krav

- **Säkerhet**: CSRF, session-hantering, 2FA, rollbaserad åtkomstkontroll (RBAC)
- **Mobilanpassning**: Responsiv design, touch-vänlig
- **Tema**: Mörkt (mörkgrå/blå) + ljust, växlingsbar från Min Sida
- **Rapporter**: Varje modul har rapportfunktion (PDF/utskrift/delning)
- **Integrationer**: Bankbetalningar, fakturamottagning via e-post (framtid)
- **Single tenant** med multi-tenant förberedelse
- **SaaS**: Backend för systemägare (kunder, domäner, abonnemang)

---

## Nästa steg (byggordning)

| Steg | Modul | Beroenden | Status |
|------|-------|-----------|--------|
| ~~1~~ | ~~Maskinhierarki~~ | — | ✅ Klar |
| ~~2~~ | ~~Underhåll (felanmälan + arbetsorder)~~ | Maskinhierarki, Personal | ✅ Grund klar |
| ~~3~~ | ~~HR (certifikat)~~ | Personal | ✅ Grund klar |
| ~~4~~ | ~~Lager (grund)~~ | Artiklar | ✅ Grund klar |
| ~~5~~ | ~~Min Sida~~ | Personal | ✅ Klar |
| **6** | **Inköp** | Leverantörer, Artiklar | 🔜 Nästa |
| 7 | Ekonomi | Kunder, Leverantörer | ⬜ |
| 8 | Produktion | Maskinhierarki | ⬜ |
| 9 | Sales | Kunder, Produktion | ⬜ |
| 10 | H/S | Personal | ⬜ |
| 11 | HR komplettering (närvaro, löner, utbildningar) | HR grund | ⬜ |
| 12 | Projekt | — | ⬜ |
| 13 | Rapportmodul + PDF | Alla moduler | ⬜ |
| 14 | Språkstöd (i18n) | — | ⬜ |
| 15 | AI-modul | Underhåll | ⬜ |
| 16 | SaaS Backend | — | ⬜ |

---

*Detta dokument uppdateras löpande när moduler färdigställs.*
