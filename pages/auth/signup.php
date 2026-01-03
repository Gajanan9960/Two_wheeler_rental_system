<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';
include '../../includes/mailer.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $username = htmlspecialchars($_POST['username']);

    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Invalid email format.";
    } elseif (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        try {
            // Check if email exists
            $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->fetch()) {
                $error = "Email already registered.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$username, $email, $hashedPassword])) {
                    sendEmail($email, "Welcome to Ride-ease!", "Hi $username,\n\nThank you for signing up with Ride-ease. We are excited to have you on board!");
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed.";
                }
            }
        } catch (PDOException $e) {
 error_log("Registration DB Error: " . $e->getMessage());
            $error = "An unexpected error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up - Ride-ease</title>
  <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
  <div class="login-container">
    <form method="POST" action="">
      <h2>Sign Up</h2>
      <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
      <?php if ($error): ?>
        <p class="error" style="color: red;"><?php echo $error; ?></p>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
      <p>Already have an account? <a href="login.php">Sign In</a></p>
      <p><a href="../../index.php">Back to Home</a></p>
      <p class="admin-link" style="margin-top: 20px; font-size: 0.85rem;"><a href="../admin/login.php" style="color: #444; font-weight: 600; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">Admin Login</a></p>
      <div style="margin-top: 10px; font-size: 0.75rem; color: #777; background: #f9f9f9; padding: 10px; border-radius: 5px; border: 1px dashed #ccc;">
          <strong>Default Admin Credentials:</strong><br>
          Email: admin@ride-ease.com<br>
          Password: password123
      </div>
    </form>
  </div>
</body>
</html>