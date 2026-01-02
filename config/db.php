<?php
$host = "localhost";
$user = "app_user";
$pass = "AppUserPassword@123";
$db_name = "ride_ease";

// Enable error reporting for debugging (remove in production)
mysqli_report(MYSQLI_REPORT_OFF);

try {
    $conn = new mysqli($host, $user, $pass, $db_name);
    if ($conn->connect_error) {
        throw new Exception($conn->connect_error);
    }
} catch (Exception $e) {
    // Graceful error handling
    die("
    <div style='font-family: sans-serif; padding: 20px; border: 1px solid #f5c6cb; background: #f8d7da; color: #721c24; max-width: 600px; margin: 50px auto; border-radius: 5px;'>
        <h2 style='margin-top:0;'>Database Connection Error</h2>
        <p>Could not connect to the database. This usually means the user <strong>$user</strong> does not have permission to access the database <strong>$db_name</strong>.</p>
        <p><strong>Technical Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>
        <hr style='border: 0; border-top: 1px solid #f5c6cb;'>
        <h3>How to Fix:</h3>
        <ol>
            <li>Open your MySQL interface (phpMyAdmin or Terminal) as <strong>root</strong>.</li>
            <li>Run the following command to grant access:</li>
        </ol>
        <pre style='background: #fff; padding: 10px; border-radius: 5px; border: 1px solid #ccc;'>GRANT ALL PRIVILEGES ON $db_name.* TO '$user'@'localhost';\nFLUSH PRIVILEGES;</pre>
    </div>
    ");
}
?>
