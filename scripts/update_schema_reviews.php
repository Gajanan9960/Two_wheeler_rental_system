<?php
require_once __DIR__ . '/../config/db.php';

echo "Checking Reviews Table Schema...\n";

try {
    // Check if table exists
    $result = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='reviews'");
    
    if (!$result->fetch()) {
        echo "[INFO] Creating 'reviews' table...\n";
        $sql = "CREATE TABLE reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            rating INTEGER NOT NULL,
            comment TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        $conn->exec($sql);
        echo "[SUCCESS] 'reviews' table created.\n";
    } else {
        echo "[INFO] 'reviews' table already exists.\n";
    }

} catch (PDOException $e) {
    echo "[ERROR] Schema Check Failed: " . $e->getMessage() . "\n";
    exit(1);
}
