# Navodila za nastavitev aplikacije

## Nastavitev upload direktorijev

Za pravilno delovanje oddaje datotek morate zagotoviti:

1. **Ustvarite upload direktorije:**
   ```bash
   mkdir -p uploads/oddaje
   mkdir -p uploads/gradiva
   ```

2. **Nastavite pravilne pravice za pisanje:**
   ```bash
   chmod 755 uploads
   chmod 755 uploads/oddaje
   chmod 755 uploads/gradiva
   ```

   Na Linux strežniku:
   ```bash
   chown -R www-data:www-data uploads/
   chmod -R 755 uploads/
   ```

3. **Preverite PHP nastavitve:**
   - `upload_max_filesize` - najmanj 10M (nastavljeno v .htaccess)
   - `post_max_size` - najmanj 10M (nastavljeno v .htaccess)
   - `file_uploads` - mora biti `On`

## Struktura direktorijev

```
SpletnaStranPlaceholderIme/
├── src/              # PHP datoteke
├── uploads/          # Upload direktorij (mora imeti pravice za pisanje)
│   ├── oddaje/       # Oddane naloge učencev
│   └── gradiva/      # Gradiva učiteljev
├── baza/             # SQL datoteke
└── .htaccess         # PHP nastavitve
```

## Preverjanje delovanja

1. Preverite, ali direktorij `uploads/` obstaja in ima pravice za pisanje
2. Preverite, ali PHP lahko piše v direktorij (test z uploadom)
3. Preverite, ali so datoteke dostopne preko spletnega strežnika

## Varnost

- Upload direktoriji so zaščiteni z `.htaccess` datotekami
- PHP datoteke v upload direktorijih ne bodo izvršene
- Preverjanje tipov datotek je priporočeno (lahko dodate)

## Reševanje težav

**Problem:** "Direktorij za datoteke ni zapisljiv"
- Rešitev: Nastavite pravice `chmod 755 uploads/` in poddirektorije

**Problem:** "Napaka pri shranjevanju datoteke"
- Rešitev: Preverite PHP nastavitve `upload_max_filesize` in `post_max_size`

**Problem:** Datoteke se ne prikažejo
- Rešitev: Preverite, ali so poti v bazi pravilne (`/uploads/oddaje/...`)

