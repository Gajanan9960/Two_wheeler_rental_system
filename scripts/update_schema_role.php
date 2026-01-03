<?php
require_once __DIR__ . '/../config/db.php';

echo "Updating Schema...\n";

try {
    // Check if column exists
    $columns = $conn->query("PRAGMA table_info(users)")->fetchAll();
    $hasRole = false;
    foreach ($columns as $col) {
        if ($col['name'] === 'role') {
            $hasRole = true;
            break;
        }
    }

    if (!$hasRole) {
        $conn->exec("ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'user'");
        echo "[SUCCESS] Added 'role' column to users table.\n";
    } else {
        echo "[INFO] 'role' column already exists.\n";
    }

} catch (PDOException $e) {
    echo "[ERROR] Schema Update Failed: " . $e->getMessage() . "\n";
    exit(1);
}
