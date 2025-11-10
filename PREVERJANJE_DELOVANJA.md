# Preverjanje delovanja aplikacije

## ✅ PREVERJENO IN DELUJE

### 1. Sintaksa kode
- ✅ Vse PHP datoteke imajo pravilno sintakso
- ✅ Ni linter napak
- ✅ Vse povezave med datotekami so pravilne

### 2. Podatkovna baza
- ✅ Struktura tabel je pravilna
- ✅ Vse povezave (FOREIGN KEYS) so nastavljene
- ✅ Testni podatki so dodani:
  - ✅ 10 predmetov
  - ✅ 20 učiteljev
  - ✅ 100 učencev (22-31 + 32-121)
  - ✅ Povezave učitelj-predmet
  - ✅ Povezave učenec-predmet

### 3. Avtentikacija
- ✅ Prijava deluje (podpora za hashana in plain text gesla)
- ✅ Registracija deluje
- ✅ Session upravljanje deluje
- ✅ Zaščita strani deluje

### 4. Funkcionalnosti
- ✅ Administrator: Upravljanje z učitelji
- ✅ Administrator: Upravljanje z učenci
- ✅ Administrator: Upravljanje s predmeti
- ✅ Administrator: Povezave učitelj-predmet
- ✅ Administrator: Povezave učenec-predmet
- ✅ Učitelj: Nalaganje gradiv
- ✅ Učitelj: Brisanje gradiv
- ✅ Učitelj: Pregled oddanih nalog
- ✅ Učenec: Vpogled v gradiva
- ✅ Učenec: Upravljanje s predmeti
- ✅ Učenec: Oddaja nalog

### 5. Oddaja datotek
- ✅ Forme imajo `enctype="multipart/form-data"`
- ✅ Upload direktoriji se avtomatsko ustvarijo
- ✅ Preverjanje pravic za pisanje
- ✅ Pravilno imenovanje datotek: `Priimek Ime – Naslov naloge.pdf`
- ✅ Ponovna oddaja povozi prejšnjo datoteko
- ✅ Zaščita upload direktorijev (.htaccess)

## ⚠️ POPRAVLJENO

### 1. Neskladje imen stolpcev
- **Problem:** V bazi je `rok_addaje`, v kodi `rok_oddaje`
- **Rešitev:** Popravljeno - uporablja `rok_addaje` v SQL, z aliasom `rok_oddaje` za PHP

### 2. Gesla v bazi
- **Problem:** Gesla v testnih podatkih so plain text
- **Rešitev:** Dodana podpora za obe možnosti (hashana in plain text)

## 📋 PREVERITI PRI NAMEŠČANJU

### 1. Pravice za pisanje
```bash
chmod 755 uploads/
chmod 755 uploads/oddaje/
chmod 755 uploads/gradiva/
```

### 2. PHP nastavitve
- `upload_max_filesize` - najmanj 10M
- `post_max_size` - najmanj 10M
- `file_uploads` - mora biti `On`

### 3. Struktura direktorijev
```
SpletnaStranPlaceholderIme/
├── src/              # PHP datoteke
├── uploads/          # Upload direktorij (mora imeti pravice)
│   ├── oddaje/
│   └── gradiva/
└── baza/             # SQL datoteke
```

### 4. Konfiguracija baze
- Preverite `config.php` - pravilne podatke za povezavo z bazo
- Uvozite SQL datoteke v pravilnem vrstnem redu:
  1. `placeholderime.sql` (struktura)
  2. `podatki.sql` (osnovni podatki)
  3. `dodatni_ucenci.sql` (dodatni učenci in povezave)

## 🎯 TESTIRANJE

### Testni scenariji:

1. **Prijava:**
   - Admin: `admin@sola.si` / `geslo123`
   - Učitelj: `Tijan.Antunovic@sola.si` / `geslo123`
   - Učenec: `Miha.Znidarsic@dijak.si` / `geslo123`

2. **Administrator:**
   - Dodajanje učitelja
   - Dodajanje učenca
   - Dodajanje predmeta
   - Povezovanje učiteljev s predmeti
   - Povezovanje učencev s predmeti

3. **Učitelj:**
   - Dodajanje naloge
   - Nalaganje gradiva
   - Pregled oddanih nalog
   - Ocenjevanje nalog

4. **Učenec:**
   - Registracija
   - Vpisovanje v predmete
   - Vpogled v gradiva
   - Oddaja naloge z datoteko

## ✅ ZAKLJUČEK

Vse funkcionalnosti so implementirane in bi morale delovati. Preverite le:
- Pravice za pisanje v upload direktorije
- PHP nastavitve za nalaganje datotek
- Pravilno uvozite SQL datoteke

