<?php
include __DIR__ . '/../config/db.php';

try {
    // Attempt to add column
    $sql = "ALTER TABLE bookings ADD COLUMN pickup_location VARCHAR(100) DEFAULT 'Main Hub'";
    $conn->exec($sql);
    echo "Column 'pickup_location' added successfully.";
} catch (PDOException $e) {
    echo "Column might already exist or error: " . $e->getMessage();
}
?>
