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
        $sql = "SELECT username, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user'] = [
                        'name' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    if ($user['role'] === 'admin') {
                        header("Location: ../admin/dashboard.php");
                    } else {
                        header("Location: ../../index.php");
                    }
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $error = "Database error.";
        }
    }
}
$conn->close();
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