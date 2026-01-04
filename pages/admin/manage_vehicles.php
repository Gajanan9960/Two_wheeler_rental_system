<?php
session_start();
include '../../config/db.php';

// RBAC
// RBAC: Ensure admin is logged in
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
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
    
    // Image Upload Logic
    $imagePath = 'assets/img/default.jpg'; // Fallback
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newFilename = uniqid('vehicle_') . '.' . $ext;
            $uploadDir = '../../assets/img/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                $imagePath = 'assets/img/' . $newFilename;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Invalid file type. Allowed: jpg, jpeg, png, webp.";
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO vehicles (name, category, price_per_day, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $category, $price, $imagePath]);
        $success = "Vehicle added successfully!";
    }
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
                <?php if (isset($error)) echo "<p class='error' style='color:red'>$error</p>"; ?>
                <?php if (isset($success)) echo "<p class='success' style='color:green'>$success</p>"; ?>
                
                <form method="POST" action="" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="text" name="name" placeholder="Vehicle Name" required>
                    <select name="category" required>
                        <option value="bike">Bike</option>
                        <option value="scooter">Scooter</option>
                        <option value="ebike">E-Bike</option>
                    </select>
                    <input type="number" step="0.01" name="price" placeholder="Price per Day" required>
                    <label style="display:block; margin: 10px 0 5px; font-size:0.9rem;">Vehicle Image:</label>
                    <input type="file" name="image" accept="image/*" required style="padding: 5px;">
                    <button type="submit" name="add_vehicle" class="btn-admin btn-primary" style="width: 100%; margin-top: 10px;">Add Vehicle</button>
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
                            echo "<td>";
                            echo "<a href='edit_vehicle.php?id={$row['id']}' class='btn-admin btn-info' style='margin-right:5px;'>Edit</a>";
                            echo "<a href='?delete={$row['id']}' class='btn-admin btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                            echo "</td>";
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
