<?php
$requirements = [
    'php_version' => '7.4.0',
    'extensions' => ['mysqli', 'session', 'json']
];

$errors = [];
$warnings = [];

// Check PHP Version
if (version_compare(PHP_VERSION, $requirements['php_version'], '<')) {
    $errors[] = "PHP version " . $requirements['php_version'] . " or higher is required. Current version: " . PHP_VERSION;
}

// Check Extensions
foreach ($requirements['extensions'] as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "Extension '$ext' is required but not loaded.";
        if ($ext === 'mysqli') {
            $warnings[] = "On Windows (XAMPP), make sure 'extension=mysqli' is uncommented in php.ini.";
        }
    }
}

// Check Database Config
$config_file = __DIR__ . '/config/db.php';
if (!file_exists($config_file)) {
    $errors[] = "Configuration file 'config/db.php' not found.";
} else {
    // Try to include and check connection 
    // We suppress output to avoid header issues if this script is included elsewhere
    ob_start();
    include $config_file;
    ob_end_clean();
    
    if (isset($conn) && $conn->connect_error) {
        $errors[] = "Database connection failed: " . $conn->connect_error;
        $warnings[] = "For XAMPP on Windows, the default user is usually 'root' with NO password. Update config/db.php if needed.";
    } elseif (!isset($conn)) {
        $warnings[] = "Could not verify database connection variable \$conn.";
    }
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
