<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/User.php';

$user = new User();

// 1. Návrat z Googlu (máme parametr ?code=...)
if (isset($_GET['code'])) {
    // Výměna kódu za Access Token
    $params = [
        'code'          => $_GET['code'],
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URL,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://oauth2.googleapis.com/token');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenInfo = json_decode($response, true);

    if (isset($tokenInfo['access_token'])) {
        // Získání dat o uživateli pomocí tokenu
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $tokenInfo['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userData = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // Přihlášení přes naši třídu User
        if (isset($userData['id'])) {
            $user->loginWithGoogle($userData);
            header("Location: index.php"); // Nebo zpět do košíku
            exit;
        }
    }

    die("Chyba přihlášení přes Google.");
}

// 2. Start přihlášení (přesměrování na Google)
$authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URL,
        'response_type' => 'code',
        'scope'         => 'email profile',
        'access_type'   => 'online'
    ]);

header("Location: $authUrl");
exit;
