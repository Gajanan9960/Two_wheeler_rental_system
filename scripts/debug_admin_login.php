<?php
require_once __DIR__ . '/../config/db.php';

echo "Debugging Admin Login...\n";

$email = 'admin@ride-ease.com';
$password = 'password123';

try {
    // 1. Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "[FAIL] User '$email' NOT FOUND in database.\n";
        
        // Attempt to create it
        echo "[INFO] Attempting to create admin user...\n";
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
        if ($insert->execute(['Admin', $email, $hash])) {
            echo "[SUCCESS] Admin user created.\n";
        } else {
            echo "[FAIL] Could not create admin user.\n";
        }
    } else {
        echo "[INFO] User found.\n";
        echo " - ID: " . $user['id'] . "\n";
        echo " - Role: " . ($user['role'] ?? 'NULL') . "\n"; // Check role
        
        // 2. Verify Password
        if (password_verify($password, $user['password'])) {
            echo "[PASS] Password matches.\n";
        } else {
            echo "[FAIL] Password does NOT match.\n";
            // Update password
            echo "[INFO] Updating password...\n";
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->execute([$newHash, $user['id']]);
            echo "[SUCCESS] Password updated.\n";
        }

        // 3. Verify Role
        if (($user['role'] ?? '') !== 'admin') {
            echo "[FAIL] Role is '" . ($user['role'] ?? 'NULL') . "'. Expected 'admin'.\n";
            // Update role
            echo "[INFO] Updating role to 'admin'...\n";
            $update = $conn->prepare("UPDATE users SET role = 'admin' WHERE id = ?");
            $update->execute([$user['id']]);
            echo "[SUCCESS] Role updated.\n";
        } else {
            echo "[PASS] Role is correct.\n";
        }
    }

} catch (PDOException $e) {
    echo "[ERROR] Database Error: " . $e->getMessage() . "\n";
}
