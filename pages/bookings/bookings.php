<?php
session_start();
include '../../config/db.php';
include '../../includes/mailer.php';

$page_title = "Book Your Ride - Ride-ease";
$base_path = "../../";
$extra_css = '<link rel="stylesheet" href="../../assets/css/booking_modern.css">';

// 1. Authorization
if (!isset($_SESSION['user'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: ../auth/login.php?redirect=$redirect");
    exit();
}

$user = $_SESSION['user'];
$user_id = $user['id'];

// 2. Fetch User Details (Safe)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. Get Vehicle ID
$vehicle_id = isset($_REQUEST['vehicle_id']) ? intval($_REQUEST['vehicle_id']) : (isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0);

if ($vehicle_id === 0) {
    echo "Invalid Vehicle Selection. <a href='../services.php'>Go Back</a>";
    exit();
}

// 4. Fetch Vehicle Details
$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$vehicle_id]);
$vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vehicle) {
    echo "Vehicle not found. <a href='../services.php'>Go Back</a>";
    exit();
}

// 5. Handle Form Submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_loc = $_POST['pickup_location'];
    $drop_loc = $_POST['drop_location']; // Note: Schema might not have drop_location, will verify or append to pickup?
    // Current schema only has 'pickup_location'. I'll Concatenate or just save logic.
    // Let's optimize: save "Pickup: X, Drop: Y" into pickup_location column if needed, or just Pickup.
    // The prompt asked for "pickup details". I'll concatenate for now to save data.
    $location_string = "Pickup: $pickup_loc | Drop: $drop_loc";
    
    $start_date = date('Y-m-d', strtotime($_POST['start_datetime']));
    $end_date = date('Y-m-d', strtotime($_POST['end_datetime']));
    
    $license = $_POST['license_no'];
    $payment_mode = $_POST['payment_mode'];
    $driver_age = 21; // Default or add field if missing in this form. User form didn't have age, adding/defaulting.
    // Actually user form didn't have age. I'll stick to their form fields + required backend fields. 
    // I'll set a default appropriate age or add hidden. Schema requires it? It allows NULL maybe? 
    // The schema update said "DEFAULT 18". So valid.

    // Calculate Price
    $price_per_day = $vehicle['price_per_day'];
    $diff = strtotime($end_date) - strtotime($start_date);
    $days = ceil($diff / (60 * 60 * 24));
    if ($days < 1) $days = 1;
    $total_price = $days * $price_per_day;

    try {
        $ins_sql = "INSERT INTO bookings (
            user_id, vehicle_id, start_date, end_date, total_price, status, 
            pickup_location, driver_age, license_no, payment_method
        ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";
        
        $stmt2 = $conn->prepare($ins_sql);
        if ($stmt2->execute([$user_id, $vehicle_id, $start_date, $end_date, $total_price, $location_string, 18, $license, $payment_mode])) {
            // Use the input email for confirmation, falling back to session email
            $confirm_email = !empty($_POST['customer_email']) ? $_POST['customer_email'] : $user['email'];
            
            // Email
            $emailBody = "Booking Confirmed!\nVehicle: {$vehicle['name']}\nLocation: $location_string\nDates: $start_date to $end_date\nTotal: ₹$total_price\n\nRide-ease.";
            sendEmail($confirm_email, "Booking Confirmation #{$conn->lastInsertId()}", $emailBody);
            
            $success = true;
            $message = "Booking Confirmed! Check your email for details.";
        } else {
            $message = "Error creating booking.";
        }
    } catch (PDOException $e) {
        $message = "Database Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../../includes/header.php'; ?>
    <style>
        .container { max-width: 800px; margin: 40px auto; padding: 20px; }
        fieldset { border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 25px; border-radius: 12px; background: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        legend { font-weight: 600; color: #fe5b3d; padding: 0 10px; background: #fff0ed; border-radius: 6px; }
        label { display: block; margin: 15px 0 8px; font-weight: 500; color: #334155; }
        input[type="text"], input[type="email"], input[type="tel"], input[type="datetime-local"], select {
            width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 1rem; color: #1e293b;
        }
        input:read-only { background: #f8fafc; color: #64748b; cursor: not-allowed; }
        button[type="submit"] {
            background: linear-gradient(135deg, #fe5b3d 0%, #ff8a65 100%); color: white; padding: 16px 32px; border: none; 
            border-radius: 50px; font-size: 1.1rem; font-weight: 600; cursor: pointer; width: 100%; transition: transform 0.2s;
            box-shadow: 0 10px 15px -3px rgba(254, 91, 61, 0.3);
        }
        button[type="submit"]:hover { transform: translateY(-2px); }
    </style>
</head>

<body>

<div class="container">
    
    <?php if(isset($success) && $success): ?>
        <div style="text-align:center; padding:50px; background:white; border-radius:16px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="width:80px; height:80px; background:#dcfce7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i class="fas fa-check" style="font-size:40px; color:#166534;"></i>
            </div>
            <h2 style="color:#166534; margin-bottom:10px; font-size:2rem;">Booking Confirmed!</h2>
            <p style="color:#64748b; margin-bottom:30px; font-size:1.1rem;"><?php echo $message; ?></p>
            <a href="../user/dashboard.php" style="display:inline-block; background:#166534; color:white; padding:12px 30px; text-decoration:none; border-radius:8px; font-weight:600;">Go to Dashboard</a>
        </div>
    <?php else: ?>

    <h2 style="text-align:center; color:#0f172a; margin-bottom:30px; font-size:2rem; font-weight:700;">🚲 Book Your Ride</h2>

    <?php if($message) echo "<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; text-align:center; margin-bottom:20px; border:1px solid #fecaca;'>$message</div>"; ?>

    <form action="" method="POST">

        <!-- Hidden IDs -->
        <input type="hidden" name="vehicle_id" value="<?= $vehicle['id']; ?>">

        <!-- Vehicle Details -->
        <fieldset>
            <legend>Vehicle Details</legend>
            <div style="display:flex; align-items:center; gap:20px;">
                <img src="<?= strpos($vehicle['image'], 'assets') === 0 ? '../../'.$vehicle['image'] : $vehicle['image']; ?>" style="width:100px; border-radius:10px;">
                <div style="flex:1;">
                    <label>Vehicle Name</label>
                    <input type="text" value="<?= htmlspecialchars($vehicle['name']); ?>" readonly>
                    
                    <label>Category</label>
                    <input type="text" value="<?= htmlspecialchars($vehicle['category']); ?>" readonly>
                    
                    <label>Rate (per day)</label>
                    <input type="text" value="₹<?= htmlspecialchars($vehicle['price_per_day']); ?>" readonly>
                </div>
            </div>
        </fieldset>

        <!-- User Details -->
        <fieldset>
            <legend>User Details</legend>

            <label>Name</label>
            <input type="text" name="customer_name" value="<?= htmlspecialchars($userData['username'] ?? ''); ?>" required>

            <label>Email</label>
            <input type="email" name="customer_email" value="<?= htmlspecialchars($userData['email'] ?? ''); ?>" required>

            <label>Mobile</label>
            <input type="tel" name="customer_phone" value="<?= htmlspecialchars($userData['phone'] ?? ''); ?>" required>
        </fieldset>

        <!-- Rental Duration -->
        <fieldset>
            <legend>Rental Period</legend>

            <label>Pickup Date & Time</label>
            <input type="datetime-local" name="start_datetime" required min="<?= date('Y-m-d\TH:i'); ?>">

            <label>Drop Date & Time</label>
            <input type="datetime-local" name="end_datetime" required min="<?= date('Y-m-d\TH:i'); ?>">
        </fieldset>

        <!-- Location -->
        <fieldset>
            <legend>Pickup & Drop Location</legend>

            <label>Pickup Location</label>
            <select name="pickup_location" required>
                <option value="">Select Location</option>
                <option value="Main Hub - Center City">Main Hub - Center City</option>
                <option value="Airport Station">Airport Station</option>
                <option value="Railway Plaza">Railway Plaza</option>
                <option value="North Gate Branch">North Gate Branch</option>
                <!-- Retaining user examples if needed, but aligning with my previous data -->
                <option value="Nanded">Nanded</option>
                <option value="Pune">Pune</option>
                <option value="Mumbai">Mumbai</option>
            </select>

            <label>Drop Location</label>
            <select name="drop_location">
                <option value="Same as Pickup">Same as Pickup</option>
                <option value="Nanded">Nanded</option>
                <option value="Pune">Pune</option>
                <option value="Mumbai">Mumbai</option>
            </select>
        </fieldset>

        <!-- Documents -->
        <fieldset>
            <legend>Verification</legend>

            <label>Driving License Number</label>
            <input type="text" name="license_no" required placeholder="MH12 202200123456" style="text-transform:uppercase;">

            <label style="margin-top:15px; display:flex; align-items:center; gap:10px;">
                <input type="checkbox" required style="width:auto;">
                I confirm my documents will be verified at pickup
            </label>
        </fieldset>

        <!-- Payment -->
        <fieldset>
            <legend>Payment</legend>

            <label>Payment Mode</label>
            <select name="payment_mode" required>
                <option value="Cash">Cash at Pickup</option>
                <option value="Online">Online (UPI/Card)</option>
            </select>
        </fieldset>

        <!-- Submit -->
        <button type="submit">Confirm Booking</button>

    </form>
    <?php endif; ?>
    
</div>
<br><br>
<?php include '../../includes/footer.php'; ?>
</body>
</html>