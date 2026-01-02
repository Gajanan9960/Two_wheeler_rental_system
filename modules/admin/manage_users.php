<?php
session_start();
include '../../config/db.php';

// RBAC
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Delete (optional, but requested control)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id=? AND role='user'"); // Protect admin from self-delete loop here (basic)
    $stmt->execute([$id]);
    header("Location: manage_users.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>User Management</h2>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>#{$row['id']}</td>";
                        echo "<td><strong>{$row['username']}</strong></td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td><span style='background:#eee; padding:3px 8px; border-radius:4px;'>User</span></td>";
                        echo "<td>{$row['created_at']}</td>";
                        echo "<td>";
                        echo "<a href='?delete={$row['id']}' class='btn-admin btn-danger' onclick='return confirm(\"Delete this user?\")'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
