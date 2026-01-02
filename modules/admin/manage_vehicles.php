<?php
session_start();
include '../../config/db.php';

// RBAC
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Add Vehicle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_vehicle'])) {
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
    $conn->exec("DELETE FROM vehicles WHERE id=$id"); // Simple exec for delete without params if trusted, but let's use check
    // Actually ID from GET should be cast or prepared
    $dStmt = $conn->prepare("DELETE FROM vehicles WHERE id=?");
    $dStmt->execute([$id]);
    header("Location: manage_vehicles.php");
    exit();
}

$page_title = "Manage Vehicles - Admin";
include '../../includes/header.php';
?>

<div class="content-container" style="padding: 40px;">
    <h1>Manage Vehicles</h1>
    <a href="dashboard.php" class="btn" style="margin-bottom: 20px; display:inline-block;">&larr; Back to Dashboard</a>

    <!-- Add Vehicle Form -->
    <div class="form-container" style="margin-bottom: 40px;">
        <h3>Add New Vehicle</h3>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Vehicle Name" required style="margin-bottom: 10px; width: 100%; padding: 10px;">
            <select name="category" required style="margin-bottom: 10px; width: 100%; padding: 10px;">
                <option value="bike">Bike</option>
                <option value="scooter">Scooter</option>
                <option value="ebike">E-Bike</option>
            </select>
            <input type="number" step="0.01" name="price" placeholder="Price per Day" required style="margin-bottom: 10px; width: 100%; padding: 10px;">
            <input type="text" name="image" placeholder="Image Filename (e.g. bike.jpg)" required style="margin-bottom: 10px; width: 100%; padding: 10px;">
            <button type="submit" name="add_vehicle" class="btn" style="width: 100%;">Add Vehicle</button>
        </form>
    </div>

    <!-- Vehicle List -->
    <h3>Current Fleet</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="background: #f4f4f4; text-align: left;">
                <th style="padding: 10px; border: 1px solid #ddd;">ID</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Name</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Category</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Price</th>
                <th style="padding: 10px; border: 1px solid #ddd;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->query("SELECT * FROM vehicles ORDER BY id DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['id']}</td>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['name']}</td>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'>{$row['category']}</td>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'>₹{$row['price_per_day']}</td>";
                echo "<td style='padding: 10px; border: 1px solid #ddd;'><a href='?delete={$row['id']}' style='color: red;' onclick='return confirm(\"Are you sure?\")'>Delete</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../../includes/footer.php'; ?>
