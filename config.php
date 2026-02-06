<?php
/**
 * Lecduit - Globální konfigurace, Market Context a Překlady
 */
session_start();
require_once __DIR__ . '/Database.php';

$host = $_SERVER['HTTP_HOST'];

// --- 1. DETEKCE TRHU A MĚNY ---
$market = [
    'id'        => 'sk',
    'currency'  => '€',
    'price_col' => 'price_sk',
    'def_lang'  => 'sk'
];

if (strpos($host, 'lecduit.cz') !== false) {
    $market = ['id' => 'cz', 'currency' => 'Kč', 'price_col' => 'price_cz', 'def_lang' => 'cz'];
} elseif (strpos($host, 'lecduit.pl') !== false) {
    $market = ['id' => 'pl', 'currency' => 'zł', 'price_col' => 'price_pl', 'def_lang' => 'pl'];
} elseif (strpos($host, 'lecduit.eu') !== false) {
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
        'contact' => 'Kontakt', 'visit_web' => 'Navštíviť web', 'more_info' => 'Viac informácií'
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
        'contact' => 'Kontakt', 'visit_web' => 'Navštívit web', 'more_info' => 'Více informací'
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
        'contact' => 'Kontakt', 'visit_web' => 'Odwiedź stronę', 'more_info' => 'Więcej informacji'
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
        'contact' => 'Contact', 'visit_web' => 'Visit website', 'more_info' => 'More info'
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
        'contact' => 'Kontakt', 'visit_web' => 'Webseite besuchen', 'more_info' => 'Mehr Info'
    ]
];

$t = $txt[$lang];
$market_id = $market['id'];
$currency  = $market['currency'];
$price_col = $market['price_col'];
$suffix    = '_' . $lang;
$pdo = Database::getInstance();

function formatPrice($amount) {
    global $currency;
    $decimals = ($currency === '€') ? 2 : 0;
    return number_format($amount, $decimals, ',', ' ') . ' ' . $currency;
}

function h($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}