<?php
session_start();
include '../../config/db.php';

// RBAC
// RBAC: Ensure admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
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

// ... logic for status update remains ...

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Manage Bookings</h2>
        </div>

        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Vehicle</th>
                        <th>Dates</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
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
                                echo "<td>#{$row['id']}</td>";
                                echo "<td>{$row['username']}</td>";
                                echo "<td>{$row['vehicle_name']}</td>";
                                echo "<td>{$row['start_date']} <br>to<br> {$row['end_date']}</td>";
                                echo "<td>₹{$row['total_price']}</td>";
                                echo "<td><span style='color: $statusColor; font-weight: bold;'>".ucfirst($row['status'])."</span></td>";
                                echo "<td>";
                                if ($row['status'] == 'pending') {
                                    echo "<a href='?action=confirmed&id={$row['id']}' class='btn-admin btn-success' style='margin-right:5px;'>Confirm</a>";
                                    echo "<a href='?action=cancelled&id={$row['id']}' class='btn-admin btn-danger'>Cancel</a>";
                                } elseif ($row['status'] == 'confirmed') {
                                    echo "<a href='?action=completed&id={$row['id']}' class='btn-admin btn-info'>Complete</a>";
                                } else {
                                    echo "-";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align: center;'>No bookings found.</td></tr>";
                        }
                    } catch (PDOException $e) {
                         echo "<tr><td colspan='7'>Error loading bookings.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
