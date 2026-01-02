<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/db.php';

echo "Database connection successful.\n";

$result = $conn->query("SHOW TABLES");
if ($result) {
    echo "Tables found:\n";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "\n";
    }
} else {
    echo "Error listing tables: " . $conn->error . "\n";
}

$userCount = $conn->query("SELECT COUNT(*) FROM users");
if ($userCount) {
    $row = $userCount->fetch_array();
    echo "Number of users: " . $row[0] . "\n";
} else {
    echo "Error counting users (maybe table 'users' missing?): " . $conn->error . "\n";
}
?>
