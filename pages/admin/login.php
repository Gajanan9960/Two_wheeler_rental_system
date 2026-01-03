<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check in users table for admin role
            $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE email = ? AND role = 'admin'");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['user'] = [
                    'id' => $admin['id'],
                    'name' => $admin['username'],
                    'email' => $email,
                    'role' => 'admin'
                ];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid admin credentials or access denied.";
            }
        } catch (PDOException $e) {
            $error = "Database Error.";
            error_log($e->getMessage()); // Log error for debugging
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Ride-ease</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }
        .login-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box; /* Fix padding causing overflow */
        }
        .login-box button {
            width: 100%;
            padding: 12px;
            background: #fe5b3d;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Panel</h2>
        <div style="background: #e8f4fd; border: 1px dashed #2196F3; padding: 10px; margin-bottom: 20px; border-radius: 5px; font-size: 0.85rem; color: #0d47a1; text-align: left;">
            <strong>Default Credentials:</strong><br>
            Email: admin@ride-ease.com<br>
            Password: password123
        </div>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p style="margin-top: 20px; font-size: 0.9rem;"><a href="../../index.php" style="color: #666; text-decoration: none;">&larr; Back to Website</a></p>
    </div>
</body>
</html>
