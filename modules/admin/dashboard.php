<?php
session_start();
include '../../config/db.php';

// RBAC: Ensure user is logged in and is an admin
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
    // If not admin, redirect to login or home
    header("Location: ../auth/login.php?error=Access Denied");
    exit();
}

$page_title = "Admin Dashboard - Ride-ease";
$base_path = "../../";
// Note: We might want a separate admin header, but for now we can use the main one or a custom one.
// Let's use the main one but we might need to hide some user links or show admin links.
// Ideally, the header should adapt based on session role.
// For now, let's include the standard header.
include '../../includes/header.php';

// Fetch Basic Stats
$stats = [];
// Total Users
// Total Users
$res = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'");
$stats['users'] = $res->fetchColumn();

// Total Vehicles
$res = $conn->query("SELECT COUNT(*) FROM vehicles");
$stats['vehicles'] = $res->fetchColumn();

// Total Bookings
$res = $conn->query("SELECT COUNT(*) FROM bookings");
$stats['bookings'] = $res->fetchColumn();

// Total Revenue
$res = $conn->query("SELECT SUM(total_price) FROM bookings WHERE status='completed' OR status='confirmed'");
$stats['revenue'] = $res->fetchColumn() ?: 0;
?>

<div class="content-container" style="padding: 40px;">
    <h1>Admin Dashboard</h1>
    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>

    <div class="stats-grid" style="display: flex; gap: 20px; margin-top: 30px;">
        <div class="stat-card" style="flex: 1; padding: 20px; background: #f4f4f4; border-radius: 8px; text-align: center;">
            <h2><?php echo $stats['users']; ?></h2>
            <p>Total Users</p>
        </div>
        <div class="stat-card" style="flex: 1; padding: 20px; background: #f4f4f4; border-radius: 8px; text-align: center;">
            <h2><?php echo $stats['vehicles']; ?></h2>
            <p>Vehicles in Fleet</p>
        </div>
        <div class="stat-card" style="flex: 1; padding: 20px; background: #f4f4f4; border-radius: 8px; text-align: center;">
            <h2><?php echo $stats['bookings']; ?></h2>
            <p>Total Bookings</p>
        </div>
        <div class="stat-card" style="flex: 1; padding: 20px; background: #d4edda; border-radius: 8px; text-align: center;">
            <h2>₹<?php echo number_format($stats['revenue']); ?></h2>
            <p>Total Revenue</p>
        </div>
    </div>

    <div class="admin-actions" style="margin-top: 40px; display: flex; gap: 20px;">
        <a href="manage_vehicles.php" class="btn" style="background-color: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;">Manage Vehicles</a>
        <a href="manage_bookings.php" class="btn" style="background-color: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px;">Manage Bookings</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
