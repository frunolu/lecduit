<?php
require_once __DIR__ . '/Database.php';

try {
    $pdo = Database::getInstance();

    echo "Running authentication migration...\n\n";

    // Add authentication columns
    $sql = "ALTER TABLE `users` 
            ADD COLUMN `password_hash` VARCHAR(255) DEFAULT NULL AFTER `email`,
            ADD COLUMN `email_verified` TINYINT(1) DEFAULT 0 AFTER `password_hash`,
            ADD COLUMN `verification_token` VARCHAR(64) DEFAULT NULL AFTER `email_verified`,
            ADD COLUMN `reset_token` VARCHAR(64) DEFAULT NULL AFTER `verification_token`,
            ADD COLUMN `reset_token_expires` DATETIME DEFAULT NULL AFTER `reset_token`";

    $pdo->exec($sql);
    echo "✓ Authentication columns added successfully!\n\n";

    // Verify the changes
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();

    echo "Current users table structure:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-25s %-20s %-10s\n", "Field", "Type", "Null");
    echo str_repeat("-", 80) . "\n";

    foreach ($columns as $col) {
        printf("%-25s %-20s %-10s\n", $col['Field'], $col['Type'], $col['Null']);
    }

    echo "\n✓ Migration completed successfully!\n";
    echo "\nYou can now delete this file: run_migration.php\n";

}
catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "⚠ Columns already exist. Migration may have been run previously.\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
    else {
        echo "✗ Migration failed!\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
}