<?php
/**
 * Import Expanded Real Experience Data
 * This script imports 36 real experiences into the database
 */

require_once __DIR__ . '/Database.php';

try {
    $pdo = Database::getInstance();

    echo "=== Starting Expanded Data Import ===\n\n";

    // First, delete old experiences
    echo "Clearing old experiences...\n";
    $pdo->exec("DELETE FROM experiences WHERE id >= 1");
    echo "✓ Old data cleared\n\n";

    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/sql.txt');

    if ($sql === false) {
        die("ERROR: Could not read sql.txt file\n");
    }

    // Remove comments and split by semicolons
    $lines = explode("\n", $sql);
    $currentStatement = '';
    $successCount = 0;

    foreach ($lines as $line) {
        $line = trim($line);

        // Skip comments and empty lines
        if (empty($line) || substr($line, 0, 2) === '--') {
            continue;
        }

        $currentStatement .= ' ' . $line;

        // If line ends with semicolon, execute the statement
        if (substr($line, -1) === ';') {
            $currentStatement = trim($currentStatement);

            try {
                if (!empty($currentStatement) && $currentStatement !== ';') {
                    $pdo->exec($currentStatement);
                    $successCount++;

                    // Show progress for INSERT statements
                    if (stripos($currentStatement, 'INSERT INTO experiences') !== false) {
                        // Extract experience title for progress
                        if (preg_match('/\((\d+),.*?\'([^\']+)\'/', $currentStatement, $matches)) {
                            echo "✓ Added: {$matches[2]}\n";
                        }
                    }
                }
            }
            catch (PDOException $e) {
                // Silently skip errors for DELETE and ALTER statements
                if (stripos($currentStatement, 'INSERT') !== false) {
                    echo "⚠ Warning: " . $e->getMessage() . "\n";
                }
            }

            $currentStatement = '';
        }
    }

    echo "\n=== Import Complete ===\n";
    echo "SQL statements executed: $successCount\n";

    // Verify the import
    echo "\n=== Verification ===\n";

    $result = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $result->fetchColumn();
    echo "Categories: $count\n";

    $result = $pdo->query("SELECT COUNT(*) FROM experiences");
    $count = $result->fetchColumn();
    echo "Experiences: $count\n";

    // Show breakdown by country
    echo "\n=== Breakdown by Country ===\n";
    $result = $pdo->query("SELECT country, COUNT(*) as cnt FROM experiences GROUP BY country ORDER BY country");
    while ($row = $result->fetch()) {
        $countryName = ['sk' => 'Slovakia', 'cz' => 'Czech Republic', 'pl' => 'Poland'][$row['country']] ?? $row['country'];
        echo "{$countryName}: {$row['cnt']}\n";
    }

    // Show breakdown by subcategory
    echo "\n=== Breakdown by Subcategory ===\n";
    $result = $pdo->query("
        SELECT s.name_sk, COUNT(e.id) as cnt 
        FROM subcategories s 
        LEFT JOIN experiences e ON e.subcategory_id = s.id 
        GROUP BY s.id, s.name_sk 
        ORDER BY cnt DESC
    ");
    while ($row = $result->fetch()) {
        echo "{$row['name_sk']}: {$row['cnt']}\n";
    }

    echo "\n✅ Expanded data successfully imported!\n";
    echo "You can now browse the catalog at http://localhost:8000\n";

}
catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}