<?php
// Function to check active state
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) == $page . '.php' ? 'active' : '';
}
?>
<div class="admin-sidebar">
    <h2>Admin Panel</h2>
    <ul>
        <li><a href="dashboard.php" class="<?php echo isActive('dashboard'); ?>">Dashboard</a></li>
        <li><a href="manage_bookings.php" class="<?php echo isActive('manage_bookings'); ?>">Bookings</a></li>
        <li><a href="manage_vehicles.php" class="<?php echo isActive('manage_vehicles'); ?>">Vehicles</a></li>
        <li><a href="manage_users.php" class="<?php echo isActive('manage_users'); ?>">Users</a></li>
        <li><a href="messages.php" class="<?php echo isActive('messages'); ?>">Messages</a></li>
        <li><a href="../../index.php">View Site</a></li>
        <li><a href="../auth/logout.php" style="color: #e74c3c;">Logout</a></li>
    </ul>
</div>
