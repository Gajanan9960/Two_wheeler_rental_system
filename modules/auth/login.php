<?php
session_start();
include '../../config/db.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Invalid email format.";
    } else {
        try {
            $sql = "SELECT id, username, email, password, role FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$email]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'] ?? 'user'
                ];
                // Redirect to Home or User Dashboard
                if (isset($_GET['redirect'])) {
                     header("Location: ".urldecode($_GET['redirect']));
                } else {
                     header("Location: ../../index.php");
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Ride-ease</title>
  <link rel="stylesheet" href="../../assets/css/auth.css">
</head>
<body>
  <div class="login-container">
    <form method="POST" action="">
      <h2>Login</h2>
      <?php if ($error): ?>
        <p class="error" style="color: red;"><?php echo $error; ?></p>
      <?php endif; ?>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="login">Login</button>
      <p><a href="#">Forgot Password?</a></p>
      <p>Don't have an account? <a href="signup.php">Sign up</a></p>
      <p><a href="../../index.php">Back to Home</a></p>
    </form>
  </div>
</body>
</html>