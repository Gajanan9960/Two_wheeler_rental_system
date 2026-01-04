<?php
include __DIR__ . '/../config/db.php';

try {
    $columns = [
        "ADD COLUMN driver_age INT DEFAULT 18",
        "ADD COLUMN license_no VARCHAR(50) DEFAULT ''",
        "ADD COLUMN payment_method VARCHAR(20) DEFAULT 'Cash'"
    ];

    foreach ($columns as $col) {
        try {
            $conn->exec("ALTER TABLE bookings $col");
            echo "Executed: ALTER TABLE bookings $col\n";
        } catch (PDOException $e) {
            echo "Skipped (maybe exists): $col\n";
        }
    }
    echo "Schema update complete.";
} catch (PDOException $e) {
    echo "Critical Error: " . $e->getMessage();
}
?>
