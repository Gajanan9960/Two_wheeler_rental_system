<?php
session_start();
include '../../config/db.php';

// RBAC: Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Basic Stats
$stats = [];
$stats['users'] = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$stats['vehicles'] = $conn->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
$stats['bookings'] = $conn->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$res = $conn->query("SELECT SUM(total_price) FROM bookings WHERE status='completed' OR status='confirmed'");
$stats['revenue'] = $res->fetchColumn() ?: 0;

// Recent Bookings
$recent_stmt = $conn->query("SELECT b.*, u.username, v.name as vehicle_name 
                            FROM bookings b 
                            JOIN users u ON b.user_id = u.id 
                            JOIN vehicles v ON b.vehicle_id = v.id 
                            ORDER BY b.created_at DESC LIMIT 5");
$recent_bookings = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Dashboard Overview</h2>
            <div class="user-info">

            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h2><?php echo $stats['users']; ?></h2>
                <p>Total Users</p>
            </div>
            <div class="stat-card">
                <h2><?php echo $stats['vehicles']; ?></h2>
                <p>Vehicles in Fleet</p>
            </div>
            <div class="stat-card">
                <h2><?php echo $stats['bookings']; ?></h2>
                <p>Total Bookings</p>
            </div>
            <div class="stat-card">
                <h2 style="color: #27ae60;">₹<?php echo number_format($stats['revenue']); ?></h2>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="admin-table-container">
            <h3>Recent Bookings</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $row): ?>
                    <tr>
                        <td>#<?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['vehicle_name']); ?></td>
                        <td><span style="font-weight:bold; color: <?php echo $row['status']=='confirmed'?'green':($row['status']=='cancelled'?'red':'orange'); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                        <td><a href="manage_bookings.php" class="btn-admin btn-info">View</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>
