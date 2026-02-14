<?php
/**
 * Lecduit - Globální konfigurace, Market Context a Překlady
 */
session_start();
require_once __DIR__ . '/Database.php';

$host = $_SERVER['HTTP_HOST'];

// --- 1. DETEKCE TRHU A MĚNY ---
$market = [
    'id' => 'sk',
    'currency' => '€',
    'price_col' => 'price_sk',
    'def_lang' => 'sk'
];

if (strpos($host, 'lecduit.cz') !== false) {
    $market = ['id' => 'cz', 'currency' => 'Kč', 'price_col' => 'price_cz', 'def_lang' => 'cz'];
}
elseif (strpos($host, 'lecduit.pl') !== false) {
    $market = ['id' => 'pl', 'currency' => 'zł', 'price_col' => 'price_pl', 'def_lang' => 'pl'];
}
elseif (strpos($host, 'lecduit.eu') !== false) {
    $market = ['id' => 'eu', 'currency' => '€', 'price_col' => 'price_sk', 'def_lang' => 'en'];
}

// --- 2. LOGIKA JAZYKA ---
if (isset($_GET['lang'])) {
    $allowed_langs = ['sk', 'cz', 'pl', 'en', 'de'];
    if (in_array($_GET['lang'], $allowed_langs)) {
        $_SESSION['lang'] = $_GET['lang'];
    }
}
$lang = $_SESSION['lang'] ?? $market['def_lang'];

// --- 4. NASTAVENÍ OAUTH (GOOGLE) ---
// Tyto hodnoty nahraď těmi, které získáš v Google Console
define('GOOGLE_CLIENT_ID', 'TVOJE_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'TVOJE_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/login_google.php');

// --- 3. KOMPLETNÍ PŘEKLADOVÝ SLOVNÍK ---
$txt = [
    'sk' => [
        'validity' => 'Platnosť 12 mesiacov', 'delivery' => 'E-mailom ihneď', 'duration' => 'Trvanie',
        'buy' => 'KÚPIŤ VOUCHER', 'secure' => 'Zabezpečená platba cez Lecduit Pay', 'back' => 'Späť',
        'desc' => 'Popis zážitku', 'price_for' => 'Cena poukazu', 'added' => 'Pridané do košíka!',
        'view_cart' => 'Pozrieť košík', 'katalog' => 'Katalóg', 'location' => 'Lokalita',
        'radius' => 'Okruh', 'country' => 'Krajina', 'filter_btn' => 'Filtrovať',
        'checkout' => 'Pokladňa', 'your_cart' => 'Váš košík', 'empty_cart' => 'Váš košík je prázdny',
        'f_name' => 'Meno', 'l_name' => 'Priezvisko', 'email' => 'E-mail', 'phone' => 'Telefón',
        'street' => 'Ulica a č.p.', 'city' => 'Mesto', 'zip' => 'PSČ', 'country_billing' => 'Krajina',
        'finish_order' => 'Dokončiť objednávku', 'total' => 'Celkom k úhrade', 'remove' => 'Odstrániť',
        'contact' => 'Kontakt', 'visit_web' => 'Navštíviť web', 'more_info' => 'Viac informácií',
        'catalog_info' => 'Toto je katalóg. Pre objednanie kontaktujte poskytovateľa.',
        'login' => 'Prihlásiť sa', 'register' => 'Registrovať', 'password' => 'Heslo',
        'password_confirm' => 'Potvrdiť heslo', 'forgot_password' => 'Zabudnuté heslo?',
        'no_account' => 'Nemáte účet?', 'have_account' => 'Máte účet?',
        'login_with_google' => 'Prihlásiť cez Google', 'or' => 'alebo',
        'register_success' => 'Registrácia úspešná! Skontrolujte e-mail.',
        'invalid_credentials' => 'Nesprávny e-mail alebo heslo',
        'email_exists' => 'E-mail už existuje', 'password_min' => 'Heslo musí mať min. 8 znakov',
        'passwords_not_match' => 'Heslá sa nezhodujú',
        'reset_email_sent' => 'Link na obnovenie hesla bol odoslaný',
        'password_reset_success' => 'Heslo bolo zmenené',
        'invalid_token' => 'Neplatný alebo expirovaný token',
        'email_verified' => 'E-mail bol overený', 'create_account' => 'Vytvoriť účet',
        'reset_password' => 'Obnoviť heslo', 'send_reset_link' => 'Odoslať link',
        'back_to_login' => 'Späť na prihlásenie', 'new_password' => 'Nové heslo'
    ],
    'cz' => [
        'validity' => 'Platnost 12 měsíců', 'delivery' => 'E-mailem ihned', 'duration' => 'Trvání',
        'buy' => 'KOUPIT POUKAZ', 'secure' => 'Zabezpečená platba přes Lecduit Pay', 'back' => 'Zpět',
        'desc' => 'Popis zážitku', 'price_for' => 'Cena poukazu', 'added' => 'Přidáno do košíku!',
        'view_cart' => 'Zobrazit košík', 'katalog' => 'Katalog', 'location' => 'Lokalita',
        'radius' => 'Okruh', 'country' => 'Země', 'filter_btn' => 'Filtrovat',
        'checkout' => 'Pokladna', 'your_cart' => 'Váš košík', 'empty_cart' => 'Váš košík je prázdný',
        'f_name' => 'Jméno', 'l_name' => 'Příjmení', 'email' => 'E-mail', 'phone' => 'Telefon',
        'street' => 'Ulice a č.p.', 'city' => 'Město', 'zip' => 'PSČ', 'country_billing' => 'Země',
        'finish_order' => 'Dokončit objednávku', 'total' => 'Celkem k úhradě', 'remove' => 'Odstranit',
        'contact' => 'Kontakt', 'visit_web' => 'Navštívit web', 'more_info' => 'Více informací',
        'catalog_info' => 'Toto je katalog. Pro objednání kontaktujte poskytovatele.',
        'login' => 'Přihlásit se', 'register' => 'Registrovat', 'password' => 'Heslo',
        'password_confirm' => 'Potvrdit heslo', 'forgot_password' => 'Zapomenuté heslo?',
        'no_account' => 'Nemáte účet?', 'have_account' => 'Máte účet?',
        'login_with_google' => 'Přihlásit přes Google', 'or' => 'nebo',
        'register_success' => 'Registrace úspěšná! Zkontrolujte e-mail.',
        'invalid_credentials' => 'Nesprávný e-mail nebo heslo',
        'email_exists' => 'E-mail již existuje', 'password_min' => 'Heslo musí mít min. 8 znaků',
        'passwords_not_match' => 'Hesla se neshodují',
        'reset_email_sent' => 'Link na obnovení hesla byl odeslán',
        'password_reset_success' => 'Heslo bylo změněno',
        'invalid_token' => 'Neplatný nebo expirovaný token',
        'email_verified' => 'E-mail byl ověřen', 'create_account' => 'Vytvořit účet',
        'reset_password' => 'Obnovit heslo', 'send_reset_link' => 'Odeslat link',
        'back_to_login' => 'Zpět na přihlášení', 'new_password' => 'Nové heslo'
    ],
    'pl' => [
        'validity' => 'Ważność 12 miesięcy', 'delivery' => 'E-mail natychmiast', 'duration' => 'Czas trwania',
        'buy' => 'KUP VOUCHER', 'secure' => 'Bezpieczna płatność przez Lecduit Pay', 'back' => 'Powrót',
        'desc' => 'Opis', 'price_for' => 'Cena vouchera', 'added' => 'Dodano do koszyka!',
        'view_cart' => 'Zobacz koszyk', 'katalog' => 'Katalog', 'location' => 'Lokalizacja',
        'radius' => 'Promień', 'country' => 'Kraj', 'filter_btn' => 'Filtruj',
        'checkout' => 'Zamówienie', 'your_cart' => 'Twój koszyk', 'empty_cart' => 'Koszyk jest pusty',
        'f_name' => 'Imię', 'l_name' => 'Nazwisko', 'email' => 'E-mail', 'phone' => 'Telefon',
        'street' => 'Ulica i nr', 'city' => 'Miasto', 'zip' => 'Kod pocztowy', 'country_billing' => 'Kraj',
        'finish_order' => 'Złóż zamówienie', 'total' => 'Suma do zapłaty', 'remove' => 'Usuń',
        'contact' => 'Kontakt', 'visit_web' => 'Odwiedź stronę', 'more_info' => 'Więcej informacji',
        'catalog_info' => 'To jest katalog. Aby zamówić, skontaktuj się z dostawcą.',
        'login' => 'Zaloguj się', 'register' => 'Zarejestruj', 'password' => 'Hasło',
        'password_confirm' => 'Potwierdź hasło', 'forgot_password' => 'Zapomniałeś hasła?',
        'no_account' => 'Nie masz konta?', 'have_account' => 'Masz konto?',
        'login_with_google' => 'Zaloguj przez Google', 'or' => 'lub',
        'register_success' => 'Rejestracja udana! Sprawdź e-mail.',
        'invalid_credentials' => 'Nieprawidłowy e-mail lub hasło',
        'email_exists' => 'E-mail już istnieje', 'password_min' => 'Hasło musi mieć min. 8 znaków',
        'passwords_not_match' => 'Hasła nie pasują',
        'reset_email_sent' => 'Link do resetowania hasła został wysłany',
        'password_reset_success' => 'Hasło zostało zmienione',
        'invalid_token' => 'Nieprawidłowy lub wygasły token',
        'email_verified' => 'E-mail został zweryfikowany', 'create_account' => 'Utwórz konto',
        'reset_password' => 'Zresetuj hasło', 'send_reset_link' => 'Wyślij link',
        'back_to_login' => 'Powrót do logowania', 'new_password' => 'Nowe hasło'
    ],
    'en' => [
        'validity' => '12 months validity', 'delivery' => 'Instant e-mail delivery', 'duration' => 'Duration',
        'buy' => 'BUY VOUCHER', 'secure' => 'Secure payment via Lecduit Pay', 'back' => 'Back',
        'desc' => 'Experience description', 'price_for' => 'Price', 'added' => 'Added to cart!',
        'view_cart' => 'View cart', 'katalog' => 'Catalog', 'location' => 'Location',
        'radius' => 'Radius', 'country' => 'Country', 'filter_btn' => 'Filter',
        'checkout' => 'Checkout', 'your_cart' => 'Your cart', 'empty_cart' => 'Your cart is empty',
        'f_name' => 'First Name', 'l_name' => 'Last Name', 'email' => 'E-mail', 'phone' => 'Phone',
        'street' => 'Street', 'city' => 'City', 'zip' => 'ZIP', 'country_billing' => 'Country',
        'finish_order' => 'Complete Order', 'total' => 'Total Amount', 'remove' => 'Remove',
        'contact' => 'Contact', 'visit_web' => 'Visit website', 'more_info' => 'More info',
        'catalog_info' => 'This is a catalog. To order, please contact the provider.',
        'login' => 'Login', 'register' => 'Register', 'password' => 'Password',
        'password_confirm' => 'Confirm password', 'forgot_password' => 'Forgot password?',
        'no_account' => "Don't have an account?", 'have_account' => 'Have an account?',
        'login_with_google' => 'Login with Google', 'or' => 'or',
        'register_success' => 'Registration successful! Check your email.',
        'invalid_credentials' => 'Invalid email or password',
        'email_exists' => 'Email already exists', 'password_min' => 'Password must be at least 8 characters',
        'passwords_not_match' => 'Passwords do not match',
        'reset_email_sent' => 'Password reset link has been sent',
        'password_reset_success' => 'Password has been changed',
        'invalid_token' => 'Invalid or expired token',
        'email_verified' => 'Email has been verified', 'create_account' => 'Create account',
        'reset_password' => 'Reset password', 'send_reset_link' => 'Send reset link',
        'back_to_login' => 'Back to login', 'new_password' => 'New password'
    ],
    'de' => [
        'validity' => '12 Monate Gültigkeit', 'delivery' => 'Sofort per E-Mail', 'duration' => 'Dauer',
        'buy' => 'GUTSCHEIN KAUFEN', 'secure' => 'Sichere Zahlung über Lecduit Pay', 'back' => 'Zurück',
        'desc' => 'Beschreibung', 'price_for' => 'Preis', 'added' => 'In den Warenkorb gelegt!',
        'view_cart' => 'Warenkorb anzeigen', 'katalog' => 'Katalog', 'location' => 'Standort',
        'radius' => 'Umkreis', 'country' => 'Land', 'filter_btn' => 'Filtern',
        'checkout' => 'Kasse', 'your_cart' => 'Warenkorb', 'empty_cart' => 'Warenkorb ist leer',
        'f_name' => 'Vorname', 'l_name' => 'Nachname', 'email' => 'E-Mail', 'phone' => 'Telefon',
        'street' => 'Straße', 'city' => 'Stadt', 'zip' => 'PLZ', 'country_billing' => 'Land',
        'finish_order' => 'Zahlungspflichtig bestellen', 'total' => 'Gesamtbetrag', 'remove' => 'Löschen',
        'contact' => 'Kontakt', 'visit_web' => 'Webseite besuchen', 'more_info' => 'Mehr Info',
        'catalog_info' => 'Dies ist ein Katalog. Zur Bestellung kontaktieren Sie bitte den Anbieter.',
        'login' => 'Anmelden', 'register' => 'Registrieren', 'password' => 'Passwort',
        'password_confirm' => 'Passwort bestätigen', 'forgot_password' => 'Passwort vergessen?',
        'no_account' => 'Kein Konto?', 'have_account' => 'Haben Sie ein Konto?',
        'login_with_google' => 'Mit Google anmelden', 'or' => 'oder',
        'register_success' => 'Registrierung erfolgreich! Überprüfen Sie Ihre E-Mail.',
        'invalid_credentials' => 'Ungültige E-Mail oder Passwort',
        'email_exists' => 'E-Mail existiert bereits', 'password_min' => 'Passwort muss mindestens 8 Zeichen haben',
        'passwords_not_match' => 'Passwörter stimmen nicht überein',
        'reset_email_sent' => 'Link zum Zurücksetzen des Passworts wurde gesendet',
        'password_reset_success' => 'Passwort wurde geändert',
        'invalid_token' => 'Ungültiger oder abgelaufener Token',
        'email_verified' => 'E-Mail wurde verifiziert', 'create_account' => 'Konto erstellen',
        'reset_password' => 'Passwort zurücksetzen', 'send_reset_link' => 'Link senden',
        'back_to_login' => 'Zurück zur Anmeldung', 'new_password' => 'Neues Passwort'
    ]
];

$t = $txt[$lang];
$market_id = $market['id'];
$currency = $market['currency'];
$price_col = $market['price_col'];
$suffix = '_' . $lang;
$pdo = Database::getInstance();

function formatPrice($amount)
{
    global $currency;
    $decimals = ($currency === '€') ? 2 : 0;
    return number_format($amount, $decimals, ',', ' ') . ' ' . $currency;
}

function h($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}