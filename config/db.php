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
    // Log error to file
    error_log("[" . date("Y-m-d H:i:s") . "] Database Error: " . $e->getMessage() . "\n", 3, __DIR__ . '/../logs/php_error.log');
    // Show generic message
    die("Service temporarily unavailable. Please try again later.");
}
?>
