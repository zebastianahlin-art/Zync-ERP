# ZYNC ERP — Projektbeskrivning

## Vision
Ett enkelt men skalbart, modulärt ERP-system med alla smarta funktioner som krävs i vardagen — på ETT ställe i ETT system.

## Teknisk stack
- **Backend:** PHP 8.4, MariaDB
- **Hosting:** Webbhosting med MariaDB
- **Målgrupp:** Industri
- **Arkitektur:** Single tenant (med förberedelse för multi-tenant), SaaS
- **Design:** Modern responsiv, mörkgrå/blå ton + ljust tema (växlingsbart från Min Sida)
- **Språk:** Svenska (standard) & Engelska

---

## Roller & Avdelningar

### Avdelningar
Produktion, Underhåll, Ekonomi, HR, H/S, Inköp, Lager, m.fl.

### Roller (hierarkisk)
Arbetare → Arbetsledare → Teamchef → Chef → CEO / VD

---

## Moduler

### 1. Produktion
- Produktionsplanering över linjer
- Produktionslager (råmaterial / färdigmaterial)

### 2. Underhåll
- Felanmälan
- Arbetsorderhantering (tilldelning, statushantering, historik)
- Tidrapportering & stängning av arbetsorder
- Reservdelskopplingar
- Besiktningspliktig utrustning (telfer, lyftredskap, fallskydd etc.)
- **AI-modul:** Analyserar arbetsordrar, feltyper, föreslår åtgärder, inspektioner, utbyte av delar

### 3. Ekonomi
- Översikt
- Fakturahantering (in/utgående)
- Kunder / Leverantörer (kopplat mot Inköp)
- Bokföringsmodul

### 4. HR (Human Resources)
- Personalhantering
- Lönehantering
- Närvaro / Frånvaro planering
- Rekrytering (mallar, strukturer)
- Utbildningar
- Avdelningar & Roller
- Certifikathantering (heta arbeten, truckkort etc. — skapa/ändra/radera, bifoga certifikat, utfärdande-/utgångsdatum)

### 5. H/S (Hälsa & Säkerhet)
- Risker & Faror (rapportering, riskbedömningar — skapa, redigera, tilldela)
- Audits (mallar, tilldela till chefer, arbetsmilj��-audits)
- Krishantering (kontakter, rutiner, färdig struktur)
- Nödresurser (brandsläckare, första hjälpen, hjärtstartare — lista/karta, kontroller, besiktningar)

### 6. Inköp
- Inköpsanmodan (skapa, attestera, tilldela inköpare)
- Inköpsordrar (skapande → utskick till leverantör med bilagor/avtal)
- Avtalshantering (bibliotek, utgående avtal, mallar, standardavtal: AB04, ABT06, NL10 m.fl.)
- Leverantörer (kopplat mot Ekonomi)

### 7. Lager
- Lagerhantering (reservdelar, förnödenheter)
- Inventering
- Inleveranser
- Uttag mot arbetsorder (kopplat mot Underhåll)

### 8. Projektmanagement
- Skapa projekt
- Historik över avslutade projekt
- Tidsplaner & budgetar

### 9. Sales
- Hantera kunder
- Offerter
- Orderingångar
- Kopplingar mot Produktion
- Prissättningar mot kunder

---

## Tvärgående funktioner

### Maskin-/Utrustningshierarki
- Hierarkisk struktur kopplad mellan Produktion & Underhåll
- Koppla lagerartiklar/reservdelar till maskiner
- Koppla dokument (ritningar, beskrivningar) till maskiner

### Rapportfunktion (alla moduler)
- Samla statistik
- PDF / utskrift
- Skicka dokument mellan användare i systemet

### Min Sida (inloggad användare)
- Ändra personliga uppgifter (kopplat mot HR)
- Kalender: framtida uppgifter, utbildningar, utgående certifikat, semester, sjukfrånvaro
- Lönespecar

---

## Integrationer
- Bankbetalningar
- Mottagande av fakturor via e-post

---

## Backend för systemägare (SaaS Admin)
- Kundöversikt (hyrestagare av ERP-systemet)
- Kopplad domän per kund
- Abonnemangslängd & kontrakt
- Felanmälan från kunder
- Fakturering av kunder

---

## Mappstruktur
Alla moduler i egna mappar för att undvika överfull root-katalog.

## Mobilanpassning
Fullt responsivt gränssnitt.

## Tema
Mörkgrå/blå ton (dark mode) + ljust tema, växlingsbart från Min Sida.
