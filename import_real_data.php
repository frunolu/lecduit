<?php
/**
 * Import Real Experience Data
 * This script imports real locations and vouchers into the database
 */

require_once __DIR__ . '/Database.php';

try {
    $pdo = Database::getInstance();

    echo "=== Starting Real Data Import ===\n\n";

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/real_data.sql');

    if ($sql === false) {
        die("ERROR: Could not read real_data.sql file\n");
    }

    // Split by semicolons to get individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function ($stmt) {
        return !empty($stmt) &&
        !preg_match('/^--/', $stmt) &&
        $stmt !== '';
    }
    );

    $successCount = 0;
    $errorCount = 0;

    // Execute each statement
    foreach ($statements as $statement) {
        try {
            $pdo->exec($statement);
            $successCount++;

            // Show progress for important operations
            if (stripos($statement, 'INSERT INTO categories') !== false) {
                echo "✓ Categories imported\n";
            }
            elseif (stripos($statement, 'INSERT INTO locations') !== false) {
                echo "✓ Locations imported\n";
            }
            elseif (stripos($statement, 'INSERT INTO vouchers') !== false) {
                echo "✓ Vouchers imported\n";
            }
            elseif (stripos($statement, 'TRUNCATE') !== false) {
                echo "✓ Cleared old data\n";
            }
        }
        catch (PDOException $e) {
            $errorCount++;
            // Only show errors for important operations
            if (stripos($statement, 'INSERT') !== false || stripos($statement, 'TRUNCATE') !== false) {
                echo "⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n=== Import Complete ===\n";
    echo "Statements executed: $successCount\n";
    if ($errorCount > 0) {
        echo "Warnings: $errorCount\n";
    }

    // Verify the import
    echo "\n=== Verification ===\n";

    $result = $pdo->query("SELECT COUNT(*) FROM categories");
    $count = $result->fetchColumn();
    echo "Categories: $count\n";

    $result = $pdo->query("SELECT COUNT(*) FROM experiences");
    $count = $result->fetchColumn();
    echo "Experiences: $count\n";

    // Show some sample experiences
    $result = $pdo->query("SELECT title_sk, country FROM experiences WHERE id >= 100 LIMIT 5");
    $samples = $result->fetchAll();
    if (!empty($samples)) {
        echo "\nSample experiences added:\n";
        foreach ($samples as $exp) {
            echo "  - {$exp['title_sk']} ({$exp['country']})\n";
        }
    }

    echo "\n✅ Real data successfully imported!\n";
    echo "You can now browse the catalog at http://localhost:8000\n";

}
catch (PDOException $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}