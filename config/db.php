<?php
// Auto-initialize if needed
require_once __DIR__ . '/init_db.php';

$db_file = __DIR__ . '/../database/ride_ease.db';

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enable Foreign Keys
    $conn->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
