<?php
session_start();
include '../../config/db.php';

// RBAC
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle Add Vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
    include '../../includes/csrf.php';
    verifyCSRFToken($_POST['csrf_token']);
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $image = 'assets/img/' . $_POST['image']; // Simple handling for now

    $stmt = $conn->prepare("INSERT INTO vehicles (name, category, price_per_day, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $category, $price, $image]);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Removed insecure exec call
    // Actually ID from GET should be cast or prepared
    $dStmt = $conn->prepare("DELETE FROM vehicles WHERE id=?");
    $dStmt->execute([$id]);
    header("Location: manage_vehicles.php");
    exit();
}

// ... logic remains ...
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Manage Vehicles</h2>
        </div>

        <div style="display: flex; gap: 20px;">
            <!-- Add Vehicle Form -->
            <div class="admin-table-container" style="flex: 1;">
                <h3>Add New Vehicle</h3>
                <form method="POST" action="" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="text" name="name" placeholder="Vehicle Name" required>
                    <select name="category" required>
                        <option value="bike">Bike</option>
                        <option value="scooter">Scooter</option>
                        <option value="ebike">E-Bike</option>
                    </select>
                    <input type="number" step="0.01" name="price" placeholder="Price per Day" required>
                    <input type="text" name="image" placeholder="Image Filename (e.g. bike.jpg)" required>
                    <button type="submit" name="add_vehicle" class="btn-admin btn-primary" style="width: 100%;">Add Vehicle</button>
                </form>
            </div>

            <!-- Vehicle List -->
            <div class="admin-table-container" style="flex: 2;">
                <h3>Current Fleet</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Img</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            // Fix image access
                            $img = '../../' . $row['image'];
                            echo "<tr>";
                            echo "<td><img src='$img' width='50' style='border-radius:5px;'></td>";
                            echo "<td>{$row['name']}</td>";
                            echo "<td>{$row['category']}</td>";
                            echo "<td>₹{$row['price_per_day']}</td>";
                            echo "<td><a href='?delete={$row['id']}' class='btn-admin btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
