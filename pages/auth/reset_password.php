<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';

$message = "";
$messageType = "";
$validToken = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Validate token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > datetime('now')");
    $stmt->execute([$token]);
    if ($stmt->fetch()) {
        $validToken = true;
    } else {
        $message = "Invalid or expired token.";
        $messageType = "error";
    }
} else {
    $message = "No token provided.";
    $messageType = "error";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    verifyCSRFToken($_POST['csrf_token']);
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass === $confirm_pass) {
        $token = $_POST['token'];
        // Get email again
        $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        $email = $stmt->fetchColumn();

        if ($email) {
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            if ($upd->execute([$hashed, $email])) {
                // Delete used token
                $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                $del->execute([$email]);
                
                $message = "Password successfully reset! <a href='login.php'>Login now</a>";
                $messageType = "success";
                $validToken = false; // Hide form
            } else {
                $message = "Error resetting password.";
                $messageType = "error";
            }
        }
    } else {
        $message = "Passwords do not match.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Ride-ease</title>
    <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
    <div class="login-container">
        <h2>Reset Password</h2>
        <?php if ($message): ?>
            <div style="padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; background-color: <?php echo ($messageType == 'success') ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo ($messageType == 'success') ? '#155724' : '#721c24'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($validToken): ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
            
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
            <button type="submit">Reset Password</button>
        </form>
        <?php endif; ?>
        
        <?php if (!$validToken && $messageType == 'error'): ?>
            <p style="text-align: center;"><a href="forgot_password.php">Request a new link</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
