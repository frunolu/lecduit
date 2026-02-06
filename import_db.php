
<?php
/**
 * Lecduit - Databázový Importér z .sql súboru
 */

require_once __DIR__ . '/Database.php';

$sqlFile = __DIR__ . '/database.sql';

if (!file_exists($sqlFile)) {
    die("❌ Chyba: Súbor database.sql nebol nájdený!");
}

try {
    $pdo = Database::getInstance();

    // Načítanie obsahu SQL súboru
    $sql = file_get_contents($sqlFile);

    // Spustenie SQL príkazov
    // Poznámka: PDO::exec dokáže spracovať viacero dopytov naraz, ak sú oddelené bodkočiarkou
    $pdo->exec($sql);

// Nahraď pôvodné echo príkazy týmto:
    if (php_sapi_name() === 'cli') {
        echo "\n✅ Import z .sql súboru úspešný!\n";
        echo "Databáza bola aktualizovaná podľa súboru database.sql.\n\n";
    } else {
        echo "<h1 style='color: green;'>✅ Import z .sql súboru úspešný!</h1>";
        echo "<p>Databáza bola aktualizovaná podľa súboru <strong>database.sql</strong>.</p>";
        echo "<a href='index.php'>Späť na web</a>";
    }

} catch (PDOException $e) {
    echo "<h1 style='color: red;'>❌ Chyba pri vykonávaní SQL:</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}