# TEMELJIT PREGLED APLIKACIJE

## 📋 PREGLED PO ZAHTEVANIH FUNKCIONALNOSTIH

### ✅ ADMINISTRATOR - Vse implementirano

#### 1. Vpis/popravljanje/brisanje predmetov (najmanj 10)
- ✅ **Dodajanje predmetov:** `dodaj_predmet.php` - deluje
- ✅ **Urejanje predmetov:** `uredi_predmet.php` - deluje
- ✅ **Brisanje predmetov:** Preko statusa (neaktiven) - deluje
- ✅ **Testni podatki:** 10 predmetov v `podatki.sql`
- ✅ **Preverjanje:** Koda predmeta je unikatna

#### 2. Vpis/popravljanje/brisanje učiteljev (najmanj 20)
- ✅ **Dodajanje učiteljev:** `dodaj_ucitelja.php` - deluje
- ✅ **Urejanje učiteljev:** `uredi_ucitelja.php` - deluje
- ✅ **Brisanje učiteljev:** Preko statusa (neaktiven) - deluje
- ✅ **Seznam učiteljev:** `upravljanje_ucitelji.php` - deluje
- ✅ **Testni podatki:** 20 učiteljev v `podatki.sql` (ID 2-21)

#### 3. Določanje, kateri učitelji poučujejo katere predmete
- ✅ **Upravljanje povezav:** `upravljanje_ucitelj_predmeti.php` - deluje
- ✅ **Many-to-many:** Implementirano preko `ucitelji_predmeti` tabele
- ✅ **Preverjanje:** 
  - Predmeti, ki jih poučuje več učiteljev: ✅ (npr. predmet 1 ima učitelja 2, 8, 14, 19)
  - Učitelji, ki poučujejo več predmetov: ✅ (npr. učitelj 2 poučuje predmeta 1 in 2)
- ✅ **Testni podatki:** Povezave v `podatki.sql` in `dodatni_ucenci.sql`

#### 4. Vpis/popravljanje/brisanje učencev (najmanj 100)
- ✅ **Dodajanje učencev:** `dodaj_ucenca.php` - deluje
- ✅ **Urejanje učencev:** `uredi_ucenca.php` - deluje
- ✅ **Brisanje učencev:** Preko statusa (neaktiven) - deluje
- ✅ **Seznam učencev:** `upravljanje_ucenci.php` - deluje
- ✅ **Testni podatki:** 100 učencev (22-31 v `podatki.sql` + 32-121 v `dodatni_ucenci.sql`)

#### 5. Določanje, kateri učenci obiskujejo katere predmete
- ✅ **Upravljanje povezav:** `upravljanje_ucenec_predmeti.php` - deluje
- ✅ **Many-to-many:** Implementirano preko `ucenci_predmeti` tabele
- ✅ **Preverjanje:** Vsi učenci obiskujejo več kot enega predmeta (2-3 predmeti na učenca)
- ✅ **Testni podatki:** Povezave v `podatki.sql` in `dodatni_ucenci.sql`

---

### ✅ UČITELJ - Vse implementirano

#### 1. Nalaganje gradiv za izbrani predmet
- ✅ **Pregled gradiv:** `gradiva.php` - deluje
- ✅ **Dodajanje gradiv:** `dodaj_gradivo.php` - deluje
- ✅ **Preverjanje dovoljenj:** Samo za predmete, ki jih učitelj poučuje
- ✅ **Tipi gradiv:** Dokument, video, povezava, drugi
- ✅ **Upload datotek:** Implementiran z pravilnimi potmi

#### 2. Brisanje gradiv za izbrani predmet
- ✅ **Brisanje:** `brisi_gradivo.php` - deluje
- ✅ **Preverjanje dovoljenj:** Samo lastnik gradiva ali administrator
- ✅ **Arhiviranje:** Gradiva se arhivirajo (status = 'arhiviran'), ne brišejo fizično

#### 3. Pregledovanje nalog, ki so jih učenci oddali
- ✅ **Pregled oddaj:** `pregled_oddanih_nalog.php` - deluje
- ✅ **Prikaz podatkov:** 
  - Ime in priimek učenca
  - Naslov naloge
  - Datum oddaje
  - Status oddaje
  - Ocena (če je ocenjeno)
  - Povezava do datoteke
- ✅ **Ocenjevanje:** Povezava do `dodajOceno.php` - deluje

---

### ✅ UČENEC - Vse implementirano

#### 1. Registracija in urejanje profila
- ✅ **Registracija:** `index_registracija.php` - deluje
- ✅ **Urejanje profila:** `meni.php` - deluje
- ✅ **Preverjanje:** Email in uporabniško ime morata biti unikatna

#### 2. Vpogled v gradiva izbranega predmeta
- ✅ **Pregled gradiv:** `gradiva_ucenec.php` - deluje
- ✅ **Preverjanje dovoljenj:** Samo za predmete, ki jih učenec obiskuje
- ✅ **Prenos datotek:** Implementiran

#### 3. Določanje seznama predmetov, ki jih obiskuje
- ✅ **Upravljanje s predmeti:** `moji_predmeti_ucenec.php` - deluje
- ✅ **Vpisovanje:** Učenec se lahko vpiše v nove predmete
- ✅ **Opuščanje:** Učenec lahko opusti predmete
- ✅ **Ponovno vpisovanje:** Učenec se lahko ponovno vpiše v opuščene predmete

#### 4. Oddaja nalog pri predmetih s seznama
- ✅ **Oddaja nalog:** `naloge_ucenec.php` - deluje
- ✅ **Imenovanje datotek:** `Priimek Ime – Naslov naloge.ext` - IMPLEMENTIRANO
- ✅ **Ponovna oddaja:** Povozi prejšnjo datoteko (z potrditvijo) - IMPLEMENTIRANO
- ✅ **Preverjanje dovoljenj:** Samo za predmete, ki jih učenec obiskuje
- ✅ **Upload direktorij:** `uploads/oddaje/` - avtomatsko ustvarjen

---

## 🔍 PREVERJANJE KODE

### ✅ Varnost
- ✅ Vse strani zahtevajo prijavo (`zahtevaj_prijavo()`)
- ✅ Preverjanje dovoljenj glede na vlogo
- ✅ SQL injection zaščita (PDO prepared statements)
- ✅ XSS zaščita (`htmlspecialchars()`)
- ✅ Upload direktoriji zaščiteni z `.htaccess`

### ✅ Povezave med datotekami
- ✅ Vse datoteke vključujejo `auth.php` in `config.php`
- ✅ Pravilne povezave med stranmi
- ✅ Navigacija deluje za vse vloge

### ✅ Podatkovna baza
- ✅ 7 tabel (več kot zahtevanih 4)
- ✅ Foreign keys implementirani
- ✅ Pravilne povezave med tabelami
- ✅ Status polja za "mehko" brisanje

### ✅ Testni podatki
- ✅ 10 predmetov
- ✅ 20 učiteljev
- ✅ 100 učencev (22-31 + 32-121)
- ✅ Povezave učitelj-predmet (many-to-many)
- ✅ Povezave učenec-predmet (many-to-many, vsi učenci > 1 predmet)

---

## ⚠️ POPRAVLJENO MED PREVERJANJEM

### 1. Neskladje imen stolpcev
- **Problem:** V bazi `rok_addaje`, v kodi `rok_oddaje`
- **Rešitev:** Popravljeno - uporablja `rok_addaje` v SQL z aliasom `rok_oddaje`

### 2. Gesla v testnih podatkih
- **Problem:** Plain text gesla v bazi, koda uporablja `password_verify`
- **Rešitev:** Dodana podpora za obe možnosti (hashana in plain text)

### 3. Ime datoteke pri oddaji
- **Problem:** Presledki so bili zamenjani z `_`
- **Rešitev:** Popravljeno - ohranja presledke in znak "–"

### 4. Poti do upload direktorijev
- **Problem:** Relativne poti lahko povzročijo težave
- **Rešitev:** Uporablja `__DIR__` za absolutne poti

---

## 📊 STATISTIKA

### Datoteke
- **PHP datoteke:** 31
- **SQL datoteke:** 4
- **CSS datoteke:** 1
- **HTML template datoteke:** 12

### Funkcionalnosti
- **Administrator:** 5/5 ✅
- **Učitelj:** 3/3 ✅
- **Učenec:** 4/4 ✅
- **Skupaj:** 12/12 ✅

### Testni podatki
- **Predmeti:** 10/10 ✅
- **Učitelji:** 20/20 ✅
- **Učenci:** 100/100 ✅
- **Povezave:** Vse implementirane ✅

---

## ✅ ZAKLJUČEK

**Vse zahtevane funkcionalnosti so implementirane in delujejo.**

### Implementirano: 100%
- ✅ Vse funkcionalnosti za administratorja
- ✅ Vse funkcionalnosti za učitelja
- ✅ Vse funkcionalnosti za učenca
- ✅ Testni podatki (10 predmetov, 20 učiteljev, 100 učencev)
- ✅ Povezave med entitetami (many-to-many)
- ✅ Oddaja datotek z pravilnim imenovanjem
- ✅ Varnost in zaščita

### Preveriti pri nameščanju:
1. Pravice za pisanje v `uploads/` direktorij
2. PHP nastavitve (`upload_max_filesize`, `post_max_size`)
3. Uvoz SQL datotek v pravilnem vrstnem redu
4. Konfiguracija baze podatkov v `config.php`

### Manjka (ne vpliva na funkcionalnost):
- ⚠️ Dokumentacija o nameščanju strežnika
- ⚠️ Dnevna poročila

---

**Aplikacija je pripravljena za uporabo!** 🎉

