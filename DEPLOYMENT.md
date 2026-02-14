# 🚀 Lecduit - Deployment Checklist

## ✅ Kód je pripravený na nasadenie!

### 📦 Čo je hotové:

#### 1. **Multi-jazyková podpora** ✅
- 5 jazykov: SK, CZ, PL, EN, DE
- Všetky preklady v `config.php`
- Automatická detekcia jazyka podľa domény

#### 2. **Autentifikácia** ✅
- Email/heslo registrácia a prihlásenie
- Google OAuth integrácia
- Email verifikácia
- Reset hesla
- Všetky stránky: `login.php`, `register.php`, `verify_email.php`, `forgot_password.php`, `reset_password.php`

#### 3. **Databáza** ✅
- 15 reálnych zážitkov (SK: 7, CZ: 5, PL: 3)
- SQL súbor pripravený: `sql.txt`
- Migračný skript: `migration_auth.sql`

#### 4. **Multi-doménová podpora** ✅
- `.htaccess` súbor vytvorený
- Automatické prepínanie jazykov podľa domény:
  - `lecduit.sk` → slovenčina
  - `lecduit.cz` → čeština
  - `lecduit.pl` → poľština
  - `lecduit.eu` → angličtina

#### 5. **Katalógový režim** ✅
- Žiadne nákupné tlačidlá
- Kontaktné informácie namiesto košíka
- Upozornenie pre používateľov

---

## 📋 Kroky na nasadenie:

### 1. **Nastavte alias domény vo Websupport** 🌐
- [ ] Prihláste sa do [admin.websupport.sk](https://admin.websupport.sk)
- [ ] Pridajte alias domény:
  - [ ] `lecduit.cz`
  - [ ] `lecduit.pl`
  - [ ] `lecduit.eu`
- [ ] Nastavte DNS záznamy pre každú doménu
- [ ] Aktivujte SSL certifikáty (Let's Encrypt)

### 2. **Nahrajte súbory na server** 📤
- [ ] Pripojte sa cez FTP/SFTP
- [ ] Nahrajte všetky súbory do root priečinka
- [ ] Skontrolujte, že `.htaccess` je nahraný

### 3. **Importujte databázu** 💾
- [ ] Otvorte phpMyAdmin
- [ ] Vyberte databázu `6BG9tIxP`
- [ ] Importujte `sql.txt`
- [ ] Importujte `migration_auth.sql` (ak ešte nie je)

### 4. **Overte konfiguráciu** ⚙️
- [ ] Skontrolujte `Database.php` - prihlasovacie údaje
- [ ] Skontrolujte `config.php` - Google OAuth credentials
- [ ] Otvorte `https://lecduit.sk` a overte funkčnosť

### 5. **Testujte všetky domény** 🧪
- [ ] `https://lecduit.sk` → slovenčina
- [ ] `https://lecduit.cz` → čeština
- [ ] `https://lecduit.pl` → poľština
- [ ] `https://lecduit.eu` → angličtina

### 6. **Testujte autentifikáciu** 🔐
- [ ] Registrácia nového používateľa
- [ ] Prihlásenie emailom/heslom
- [ ] Prihlásenie cez Google
- [ ] Reset hesla

---

## 📁 Dôležité súbory:

| Súbor | Účel |
|-------|------|
| `.htaccess` | Multi-doménová konfigurácia |
| `sql.txt` | Reálne dáta na import |
| `migration_auth.sql` | Databázová migrácia pre auth |
| `config.php` | Jazykové preklady |
| `Database.php` | Databázové pripojenie |
| `User.php` | Autentifikačná logika |

---

## ⚠️ Poznámky:

### Email odosielanie
Momentálne je email odosielanie **simulované** (vypíše sa do konzoly).
Pre produkciu musíte:
1. Vytvoriť `EmailHelper.php` s SMTP nastavením
2. Nastaviť SMTP server (napr. Gmail, SendGrid, Mailgun)
3. Aktualizovať `User.php` na používanie `EmailHelper`

### Bezpečnosť
- [ ] Zmeňte databázové heslá v `Database.php`
- [ ] Použite environment variables namiesto hardcoded credentials
- [ ] Aktivujte HTTPS (odkomentujte v `.htaccess`)
- [ ] Nastavte správne Google OAuth redirect URLs

---

## 🎉 Po nasadení:

Vaša aplikácia bude fungovať na všetkých 4 doménach s automatickým prepínaním jazykov!

**Testovací príklad:**
- Návštevník otvorí `lecduit.cz` → automaticky vidí český jazyk
- Návštevník otvorí `lecduit.pl` → automaticky vidí poľský jazyk
- Používateľ môže manuálne prepnúť jazyk cez dropdown

---

## 🆘 Podpora:

Ak narazíte na problémy:
1. Skontrolujte error log vo Websupport
2. Overte, že všetky súbory sú nahrané
3. Skontrolujte databázové pripojenie
4. Overte DNS nastavenia

**Všetko je pripravené! Môžete nasadiť! 🚀**
