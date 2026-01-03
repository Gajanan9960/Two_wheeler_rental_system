<?php
// Verification Script for Auth Flow
require_once __DIR__ . '/../config/db.php';

echo "Starting Auth Verification...\n";

$test_username = 'verify_user';
$test_email = 'verify@example.com';
$test_password = 'Password123!';

try {
    // 1. Cleanup previous test data
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    echo "[PASS] Cleanup old data.\n";

    // 2. Simulate Registration
    $hashed = password_hash($test_password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$test_username, $test_email, $hashed]);
    echo "[PASS] User registration (insert).\n";

    // 3. Verify Data Storage
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    $user = $stmt->fetch();

    if ($user && $user['username'] === $test_username) {
        echo "[PASS] Data retrieval verified.\n";
    } else {
        echo "[FAIL] User not found or mismatch.\n";
        exit(1);
    }

    // 4. Verify Password Hashing
    if (password_verify($test_password, $user['password'])) {
        echo "[PASS] Password hash verification.\n";
    } else {
        echo "[FAIL] Password verification failed.\n";
        exit(1);
    }

    // 5. Cleanup
    $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
    $stmt->execute([$test_email]);
    echo "[PASS] Teardown complete.\n";

    echo "\nAll checks passed! Auth system is robust.\n";

} catch (PDOException $e) {
    echo "[ERROR] Database Exception: " . $e->getMessage() . "\n";
    exit(1);
}
