# ZYNC ERP — Projektöversikt & Roadmap

> Senast uppdaterad: 2026-03-02

---

## Vision

Ett enkelt men skalbart, modulärt ERP-system med alla smarta funktioner som krävs i vardagen — på ETT ställe, i ETT system. Målgrupp: **stora industrier**. Single-tenant med förberedelse för multi-tenant SaaS.

---

## Teknisk stack

| Komponent | Val |
|-----------|-----|
| Backend | PHP 8.4, custom MVC |
| Databas | MariaDB |
| Frontend | Tailwind CSS + Alpine.js |
| Server | Apache, mod_rewrite |
| Deploy | GitHub Actions → VPS |
| Språk | Svenska (default) + Engelska |

---

## Modulöversikt

### ✅ Klart & fungerar

| Modul | Status | Noteringar |
|-------|--------|------------|
| Auth (login/logout) | ✅ | Session-baserat, CSRF |
| Dashboard | ✅ Grund | Behöver widgets med riktig data per roll |
| Kunder | ✅ CRUD | Ska kopplas till Sales + Ekonomi |
| Leverantörer | ✅ CRUD | Ska kopplas till Inköp + Ekonomi |
| Artiklar | ✅ CRUD | Bas för Lager — behöver maskinkoppling |
| Avdelningar | ✅ CRUD | Ska tillhöra HR-modulen |
| Personal | ✅ CRUD | Ska tillhöra HR — behöver certifikat, löner, närvaro |
| Lager (grund) | ✅ | Lagerplatser, saldo, rörelser, historik |
| Roller (DB) | ✅ Tabeller | Inget UI ännu |
| Dark mode | ✅ | Fungerar |
| Responsiv design | ✅ | Tailwind |

### ❌ Saknas — nya moduler att bygga

Prioritetsordning:

| Prio | Modul | Beskrivning |
|------|-------|-------------|
| 🔴 1 | **Maskinhierarki** | Gemensam bas för Produktion + Underhåll + Lager. Hierarkisk struktur med dokument- och reservdelskopplingar |
| 🔴 2 | **Underhåll** | Felanmälan, arbetsorder (tilldelning, status, historik), tidrapportering, reservdelskoppling, besiktningspliktig utrustning, AI-analys |
| 🔴 3 | **HR (utökad)** | Certifikathantering (heta arbeten, truckkort etc.), lönehantering, närvaro/frånvaro, rekrytering, utbildningar, roller UI |
| 🔴 4 | **Lager (utökad)** | Inventering, inleveranser, uttag mot arbetsorder |
| 🔴 5 | **Produktion** | Produktionslinjer, planering, produktionslager (råmaterial/färdigmaterial) |
| 🔴 6 | **Ekonomi** | Översikt, fakturahantering (in/ut), bokföringsmodul |
| 🔴 7 | **Inköp** | Inköpsanmodan (skapa, attestera, tilldela), inköpsorder, avtalshantering (AB04, ABT06, NL10 m.fl.), leverantörskoppling |
| 🟡 8 | **H/S (Hälsa & Säkerhet)** | Riskbedömningar, audits, krishantering, nödresurser (brandsläckare, hjärtstartare m.m.) |
| 🟡 9 | **Sales** | Offerter, orderingångar, prissättning, koppling mot produktion |
| 🟡 10 | **Projekt** | Skapa projekt, tidsplaner, budgetar, historik |
| 🟡 11 | **Min Sida** | Personuppgifter, kalender (utbildningar, certifikat, semester), lönespec |
| 🟡 12 | **Rapporter** | PDF-generering, statistik, delning mellan användare |
| 🟡 13 | **Språkstöd** | i18n: Svenska (default) + Engelska |
| 🟡 14 | **SaaS Backend** | Kundhantering, domäner, abonnemang, kontrakt, felanmälan, fakturering |

---

## Kritiska kopplingar (arkitektur)



---

## Icke-funktionella krav

- **Säkerhet**: CSRF, session-hantering, rollbaserad åtkomstkontroll
- **Mobilanpassning**: Responsiv design, touch-vänlig
- **Tema**: Mörkt (mörkgrå/blå) + ljust, växlingsbar från Min Sida
- **Rapporter**: Varje modul har rapportfunktion (PDF/utskrift/delning)
- **Integrationer**: Bankbetalningar, fakturamottagning via e-post (framtid)
- **Single tenant** med multi-tenant förberedelse
- **SaaS**: Backend för systemägare (kunder, domäner, abonnemang)

---

## Byggordning (steg-för-steg)

| Steg | Modul | Beroenden |
|------|-------|-----------|
| 1 | Maskinhierarki | — |
| 2 | Underhåll (felanmälan + arbetsorder) | Maskinhierarki, Personal |
| 3 | HR utökad (certifikat, roller UI) | Personal |
| 4 | Lager utökad (uttag mot AO, inventering) | Lager, Underhåll |
| 5 | Produktion | Maskinhierarki |
| 6 | Ekonomi | Kunder, Leverantörer |
| 7 | Inköp | Leverantörer, Ekonomi |
| 8 | H/S | Personal |
| 9 | Sales | Kunder, Produktion |
| 10 | Projekt | — |
| 11 | Min Sida + Rapporter + Språk | Alla moduler |
| 12 | SaaS Backend | — |

---

*Detta dokument uppdateras löpande när moduler färdigställs.*
