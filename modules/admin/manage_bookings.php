<?php
session_start();
include '../../config/db.php';

// RBAC
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Status Update
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action']; // confirmed, cancelled, completed
    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->execute([$status, $id]);
    header("Location: manage_bookings.php");
    exit();
}

$page_title = "Manage Bookings - Admin";
include '../../includes/header.php';
?>

<div class="content-container" style="padding: 40px;">
    <h1>Manage Bookings</h1>
    <a href="dashboard.php" class="btn" style="margin-bottom: 20px; display:inline-block;">&larr; Back to Dashboard</a>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f4f4f4; text-align: left;">
                <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd;">User</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Vehicle</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Dates</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Total</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Status</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT b.*, u.username, v.name as vehicle_name 
                    FROM bookings b 
                    JOIN users u ON b.user_id = u.id 
                    JOIN vehicles v ON b.vehicle_id = v.id 
                    ORDER BY b.created_at DESC";
            
            try {
                $stmt = $conn->query($sql);
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($bookings) > 0) {
                    foreach ($bookings as $row) {
                        $statusColor = 'orange';
                        if($row['status'] == 'confirmed') $statusColor = 'green';
                        if($row['status'] == 'cancelled') $statusColor = 'red';
                        if($row['status'] == 'completed') $statusColor = 'blue';

                        echo "<tr>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['id']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['username']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['vehicle_name']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['start_date']} to {$row['end_date']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>₹{$row['total_price']}</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd; color: $statusColor; font-weight: bold;'>".ucfirst($row['status'])."</td>";
                        echo "<td style='padding: 10px; border: 1px solid #ddd;'>";
                        if ($row['status'] == 'pending') {
                            echo "<a href='?action=confirmed&id={$row['id']}' style='color: green; margin-right: 10px;'>Confirm</a>";
                            echo "<a href='?action=cancelled&id={$row['id']}' style='color: red;'>Cancel</a>";
                        } elseif ($row['status'] == 'confirmed') {
                            echo "<a href='?action=completed&id={$row['id']}' style='color: blue;'>Complete</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='padding: 20px; text-align: center;'>No bookings found.</td></tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='7'>Error loading bookings.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
