<?php
$requirements = [
    'php' => '7.4.0',
    'extensions' => ['pdo', 'pdo_sqlite', 'session', 'json'],
    'write_permissions' => [
        __DIR__ . '/database',
        __DIR__ . '/uploads' // If you have uploads
    ]
];

$errors = [];
$warnings = [];

// 1. Check PHP Version
if (version_compare(phpversion(), $requirements['php'], '<')) {
    $errors[] = "PHP version " . $requirements['php'] . " or higher is required. Current version: " . phpversion();
}

// 2. Check Extensions
foreach ($requirements['extensions'] as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "PHP Extension '$ext' is missing.";
        if ($ext === 'pdo_sqlite') {
            $warnings[] = "On Windows (XAMPP), make sure 'extension=pdo_sqlite' is uncommented in php.ini.";
        }
    }
}

// Check Database Config
$config_file = __DIR__ . '/config/db.php';
if (!file_exists($config_file)) {
    $errors[] = "Configuration file 'config/db.php' not found.";
} else {
    // 3. Check Database Connection
    // We suppress output to avoid header issues if this script is included elsewhere
    ob_start();
    try {
        include $config_file; // This file should define $conn as a PDO object
        // Test a query to ensure the connection is active
        if (isset($conn) && $conn instanceof PDO) {
            $conn->query("SELECT 1");
        } else {
            $errors[] = "Database connection variable \$conn not found or not a PDO object after including 'config/db.php'.";
        }
    } catch (Exception $e) {
        $errors[] = "Database Connection Failed: " . $e->getMessage();
        $warnings[] = "Ensure 'database/ride_ease.db' is writable and the path in config/db.php is correct.";
    }
    ob_end_clean();
}

// Output
echo "<h1>System Compatibility Check</h1>";

if (empty($errors)) {
    echo "<h3 style='color: green;'>✅ Your system is ready to run Ride-ease!</h3>";
} else {
    echo "<h3 style='color: red;'>❌ Issues Found:</h3><ul>";
    foreach ($errors as $err) {
        echo "<li style='color: red;'>$err</li>";
    }
    echo "</ul>";
}

if (!empty($warnings)) {
    echo "<h3 style='color: orange;'>⚠️ Warnings/Tips:</h3><ul>";
    foreach ($warnings as $warn) {
        echo "<li>$warn</li>";
    }
    echo "</ul>";
}

echo "<hr>";
echo "<p>OS: " . PHP_OS . "</p>";
echo "<p>PHP Location: " . PHP_BINARY . "</p>";
