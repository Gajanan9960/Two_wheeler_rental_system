<?php
session_start();
include '../../config/db.php';

// RBAC
// RBAC: Ensure admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>User Messages</h2>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>#{$row['id']}</td>";
                        echo "<td><strong>{$row['name']}</strong></td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                        echo "<td>{$row['created_at']}</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
