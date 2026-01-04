<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';

// RBAC
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manage_vehicles.php");
    exit();
}

$id = $_GET['id'];
$message = "";
$messageType = "";

// Fetch Vehicle Data
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    die("Vehicle not found.");
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCSRFToken($_POST['csrf_token']);
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    
    // Image Handling
    $imagePath = $vehicle['image']; // Default to existing
    
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
                $message = "Failed to upload image.";
                $messageType = "error";
            }
        } else {
            $message = "Invalid file type.";
            $messageType = "error";
        }
    }

    if (!$message) {
        $upd = $conn->prepare("UPDATE vehicles SET name=?, category=?, price_per_day=?, image=? WHERE id=?");
        if ($upd->execute([$name, $category, $price, $imagePath, $id])) {
            $message = "Vehicle updated successfully!";
            $messageType = "success";
            // Refresh data
            $stmt->execute([$id]);
            $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $message = "Database update failed.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="admin-content">
        <div class="admin-topbar">
            <h2>Edit Vehicle</h2>
        </div>

        <div class="admin-table-container">
            <a href="manage_vehicles.php" class="btn-admin btn-info" style="float:right; text-decoration:none;">&larr; Back to Fleet</a>
            <h3>Update Details for: <?php echo htmlspecialchars($vehicle['name']); ?></h3>

            <?php if ($message): ?>
                <div style="padding:10px; margin-bottom:15px; border-radius:5px; background: <?php echo $messageType=='success'?'#d4edda':'#f8d7da'; ?>; color: <?php echo $messageType=='success'?'#155724':'#721c24'; ?>;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" class="admin-form" style="max-width: 600px;">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <label>Vehicle Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($vehicle['name']); ?>" required>
                
                <label>Category</label>
                <select name="category" required>
                    <option value="bike" <?php echo $vehicle['category']=='bike'?'selected':''; ?>>Bike</option>
                    <option value="scooter" <?php echo $vehicle['category']=='scooter'?'selected':''; ?>>Scooter</option>
                    <option value="ebike" <?php echo $vehicle['category']=='ebike'?'selected':''; ?>>E-Bike</option>
                </select>
                
                <label>Price per Day (₹)</label>
                <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($vehicle['price_per_day']); ?>" required>
                
                <label>Current Image</label>
                <div style="margin: 10px 0;">
                    <img src="../../<?php echo $vehicle['image']; ?>" width="100" style="border-radius: 5px; border: 1px solid #ddd;">
                </div>

                <label>Change Image (Optional)</label>
                <input type="file" name="image" accept="image/*" style="padding: 5px; border: 1px solid #ccc; width: 100%;">

                <button type="submit" class="btn-admin btn-success" style="width: 100%; margin-top: 20px;">Update Vehicle</button>
            </form>
        </div>
    </div>
</body>
</html>
