<?php
session_start();
include '../../config/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_email = $_SESSION['user']['email'];
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$user_email]);
$user_id = $stmt->fetch(PDO::FETCH_COLUMN);

$page_title = "My Dashboard - Ride-ease";
$base_path = "../../";
$extra_css = '<link rel="stylesheet" href="../../assets/css/dashboard.css">';
include '../../includes/header.php';
?>

<div class="content-container">
    <h1>My Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</p>

    <div style="margin-top: 30px;">
        <h2>My Rides</h2>
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Vehicle</th>
                    <th>Dates</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT b.*, v.name as vehicle_name 
                        FROM bookings b 
                        JOIN vehicles v ON b.vehicle_id = v.id 
                        WHERE b.user_id = ?
                        ORDER BY b.created_at DESC";
                
                try {
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$user_id]);
                    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (count($bookings) > 0) {
                        foreach ($bookings as $row) {
                            $statusClass = 'status-' . strtolower($row['status']);
                            
                            echo "<tr>";
                            echo "<td>#{$row['id']}</td>";
                            echo "<td>{$row['vehicle_name']}</td>";
                            echo "<td>{$row['start_date']} to {$row['end_date']}</td>";
                            echo "<td>₹{$row['total_price']}</td>";
                            echo "<td class='$statusClass'>".ucfirst($row['status'])."</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center; padding: 30px;'>You haven't booked any rides yet. <a href='../../services.php' style='color: #fe5b3d; font-weight: bold;'>Book now!</a></td></tr>";
                    }
                } catch (PDOException $e) {
                     echo "<tr><td colspan='5'>Error loading bookings.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
