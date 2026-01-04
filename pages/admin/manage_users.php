<?php
session_start();
include '../../config/db.php';

// RBAC
// RBAC: Ensure admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Role Toggle
if (isset($_GET['make_admin'])) {
    $id = $_GET['make_admin'];
    $stmt = $conn->prepare("UPDATE users SET role='admin' WHERE id=?");
    $stmt->execute([$id]);
    header("Location: manage_users.php");
    exit();
}
if (isset($_GET['revoke_admin'])) {
    $id = $_GET['revoke_admin'];
    // Prevent self-demotion
    if ($_SESSION['user']['id'] != $id) {
        $stmt = $conn->prepare("UPDATE users SET role='user' WHERE id=?");
        $stmt->execute([$id]);
    }
    header("Location: manage_users.php");
    exit();
}

// Handle Delete (optional, but requested control)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Prevent self-delete
    if ($_SESSION['user']['id'] != $id) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?"); 
        $stmt->execute([$id]);
    }
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
                        echo "<td><span style='background:#eee; padding:3px 8px; border-radius:4px;'>".ucfirst($row['role'] ?? 'User')."</span></td>";
                        echo "<td>{$row['created_at']}</td>";
                        echo "<td>";
                        if ($row['role'] !== 'admin') {
                            echo "<a href='?make_admin={$row['id']}' class='btn-admin btn-info' onclick='return confirm(\"Make this user an Admin?\")' style='margin-right:5px; font-size:0.8rem;'>Make Admin</a>";
                        } elseif ($row['id'] != $_SESSION['user']['id']) {
                             echo "<a href='?revoke_admin={$row['id']}' class='btn-admin btn-warning' onclick='return confirm(\"Revoke Admin rights?\")' style='margin-right:5px; font-size:0.8rem;'>Revoke Admin</a>";
                        }
                        
                        // Delete Button (Prevent self delete)
                        if ($row['id'] != $_SESSION['user']['id']) {
                            echo "<a href='?delete={$row['id']}' class='btn-admin btn-danger' onclick='return confirm(\"Delete this user?\")'>Delete</a>";
                        } else {
                            echo "<span style='color:#ccc;'>Rank Locked</span>";
                        }
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
