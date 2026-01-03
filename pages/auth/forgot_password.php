<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $token = bin2hex(random_bytes(16));
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $ins->execute([$email, $token, $expiry]);

            // Mock Email Sending
            $resetLink = "http://localhost:8000/pages/auth/reset_password.php?token=" . $token;
            $message = "Reset link generated (Mock Email): <br><a href='$resetLink'>$resetLink</a>";
            $messageType = "success";
        } else {
            // Don't reveal if email exists or not for security, but for now generic message
            $message = "If an account exists with this email, a reset link has been sent.";
            $messageType = "success";
        }
    } else {
        $message = "Invalid email format.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Ride-ease</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="login-container">
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <h2>Reset Password</h2>
            <?php if ($message): ?>
                <div style="padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; background-color: <?php echo ($messageType == 'success') ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo ($messageType == 'success') ? '#155724' : '#721c24'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <p style="text-align: center; margin-bottom: 20px;">Enter your email to receive a password reset link.</p>
            <input type="email" name="email" placeholder="Your Email Address" required>
            <button type="submit">Send Reset Link</button>
            
            <p><a href="login.php">Back to Login</a></p>
        </form>
    </div>
</body>
</html>
