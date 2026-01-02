<?php
session_start();
include '../../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_email = $_SESSION['user']['email'];
$user_id_res = $conn->query("SELECT id FROM users WHERE email='$user_email'");
$user_id = $user_id_res->fetch_assoc()['id'];

$page_title = "My Dashboard - Ride-ease";
$base_path = "../../";
include '../../includes/header.php';
?>

<div class="content-container" style="padding: 40px;">
    <h1>My Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>

    <div style="margin-top: 30px;">
        <h2>My Rides</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr style="background: #f4f4f4; text-align: left;">
                    <th style="padding: 10px; border: 1px solid #ddd;">Booking ID</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Vehicle</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Dates</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Total Cost</th>
                    <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT b.*, v.name as vehicle_name 
                        FROM bookings b 
                        JOIN vehicles v ON b.vehicle_id = v.id 
                        WHERE b.user_id = $user_id
                        ORDER BY b.created_at DESC";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $statusColor = 'orange';
                        if($row['status'] == 'confirmed') $statusColor = 'green';
                        if($row['status'] == 'cancelled') $statusColor = 'red';
                        if($row['status'] == 'completed') $statusColor = 'blue';

                        echo "<tr>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>#{$row['id']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['vehicle_name']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['start_date']} to {$row['end_date']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>₹{$row['total_price']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd; color: $statusColor; font-weight: bold;'>".ucfirst($row['status'])."</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' style='padding: 20px; text-align: center;'>You haven't booked any rides yet. <a href='../../services.php'>Book now!</a></td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
