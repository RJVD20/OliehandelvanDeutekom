# Postcode Lookup — Checkout Formulier

**Datum:** 2026-06-09
**Project:** moddedadventures.nl

## Samenvatting

Voeg postcode + huisnummer lookup toe aan het checkout-formulier zodat straat, stad en provincie automatisch worden ingevuld. Aanpak: Alpine.js frontend roept een Laravel proxy-route aan, die op zijn beurt postcode.tech aanroept.

---

## 1. Formulier — Veldwijzigingen

Het huidige `address`-veld (placeholder "Straat + huisnummer") wordt gesplitst:

| Veld | Nieuw/Bestaand | Gedrag |
|---|---|---|
| Postcode | Bestaand | Ongewijzigd, triggert lookup samen met huisnummer |
| Huisnummer | **Nieuw** | Gebruiker vult in; blur triggert lookup |
| Straat | Bestaand (hernoemd van `address`) | Auto-ingevuld na lookup, bewerkbaar |
| Stad (`city`) | Bestaand | Auto-ingevuld na lookup, bewerkbaar |
| Provincie (`province`) | Bestaand | Auto-ingevuld na lookup, bewerkbaar |

**Database:** geen wijziging. Het `address`-veld slaat straat + huisnummer samengevoegd op (bv. `"Kerkstraat 10"`), net zoals nu.

**Form submit:** Het formulier heeft een verborgen `<input type="hidden" name="address">` die via Alpine.js automatisch wordt gevuld met `straat + ' ' + huisnummer` voordat het formulier wordt verzonden (`@submit` handler). De zichtbare straat-input heeft geen `name`-attribuut en wordt dus niet apart meegestuurd.

**Volgorde in het formulier:**
```
Naam
E-mail
Postcode | Huisnummer   (twee kolommen naast elkaar)
Straat                  (auto-ingevuld)
Stad                    (auto-ingevuld)
Provincie               (auto-ingevuld)
```

---

## 2. Backend — Laravel Proxy Route

**Route:** `GET /api/postcode-lookup`

**Parameters:**
- `postcode` (string, verplicht) — bv. `1234AB` of `1234 AB`
- `huisnummer` (string, verplicht) — bv. `10`

**Gedrag:**
1. Valideer dat beide parameters aanwezig zijn
2. Stuur request naar `https://postcode.tech/api/v1/postcode/full?postcode={postcode}&number={huisnummer}` via Laravel HTTP client
3. Bij succes: return JSON `{ straat, stad, provincie }`
4. Bij fout (postcode niet gevonden, API onbereikbaar): return HTTP 422 met `{ message: "Postcode niet gevonden" }`

**Auth:** geen — publiek toegankelijk (alleen checkout-gebruikers hebben er toegang toe).

**Bestand:** nieuwe method in een `PostcodeLookupController` of als closure in `routes/api.php`.

---

## 3. Frontend — Alpine.js Component

Het checkout-formulier krijgt een Alpine.js component (`x-data`).

**State:**
```js
{
  postcode: '',
  huisnummer: '',
  straat: '',
  stad: '',
  provincie: '',
  loading: false,
  error: null,
}
```

**Trigger:** `@blur` op het huisnummer-veld. Alleen uitvoeren als zowel postcode als huisnummer zijn ingevuld.

**Stappen:**
1. Zet `loading = true`, `error = null`
2. Fetch naar `/api/postcode-lookup?postcode=...&huisnummer=...`
3. Bij succes: vul `straat`, `stad`, `provincie` in
4. Bij fout: zet `error = "Postcode niet gevonden, vul handmatig in"`
5. Altijd: `loading = false`

**UX-details:**
- Tijdens laden: placeholder van straat/stad/provincie toont `...`
- Foutmelding: kleine rode tekst onder het huisnummer-veld
- Alle velden blijven bewerkbaar zodat de gebruiker altijd kan corrigeren
- Ingelogde gebruikers: bestaande adresgegevens worden vooraf ingevuld zoals nu

---

## 4. Foutafhandeling

| Situatie | Gedrag |
|---|---|
| Postcode niet gevonden | Rode melding, velden leeg en handmatig invulbaar |
| API onbereikbaar (timeout) | Zelfde als niet gevonden |
| Gebruiker corrigeert auto-ingevulde waarde | Gewoon toegestaan — velden zijn altijd editable |
| Formulier submit zonder lookup | Normale validatie — server vereist alle velden |

---

## 5. Niet in scope

- Caching van lookups (kan later toegevoegd worden)
- Huisnummertoevoeging (bv. `10A`) — buiten scope, gebruiker kan dit handmatig in het straat-veld aanpassen
- Gebruik van een betaalde API
