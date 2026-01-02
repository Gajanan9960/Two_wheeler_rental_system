<?php
session_start();
include '../../config/db.php';

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        $error = "Invalid email format.";
    } elseif (empty($username) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("sss", $username, $email, $hashedPassword);
                if ($stmt->execute()) {
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed.";
                }
                $stmt->close();
            } else {
                $error = "Database error.";
            }
        }
        $check->close();
    }
    $conn->close();
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
      <?php if ($error): ?>
        <p class="error" style="color: red;"><?php echo $error; ?></p>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign Up</button>
      <p>Already have an account? <a href="login.php">Sign In</a></p>
      <p><a href="../../index.php">Back to Home</a></p>
    </form>
  </div>
</body>
</html>