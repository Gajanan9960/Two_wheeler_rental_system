<?php
session_start();
include '../../config/db.php';

$page_title = "Rent a Bike - Ride-ease";
$base_path = "../../";
$extra_css = '<link rel="stylesheet" href="../../assets/css/booking.css">';
include '../../includes/header.php';

$message = "";
$messageType = "";

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    // Save current parameters to redirect back after login
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    echo "<script>window.location.href='../auth/login.php?redirect=$redirect';</script>";
    exit();
}

// Fetch user ID for booking
$user_email = $_SESSION['user']['email'];
$user_res = $conn->query("SELECT id FROM users WHERE email='$user_email'");
$user_id = $user_res->fetch_assoc()['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vehicle_id = $_POST['vehicle_id']; // Hidden field
    $start_date = $_POST['pickup_date'];
    $end_date = $_POST['return_date'];
    $price_per_day = $_POST['price_per_day']; // Hidden field
    
    // Calculate total price
    $diff = strtotime($end_date) - strtotime($start_date);
    $days = ceil($diff / (60 * 60 * 24));
    if ($days < 1) $days = 1; // Minimum 1 day
    $total_price = $days * $price_per_day;

    // Check Availability
    $check_sql = "SELECT * FROM bookings 
                  WHERE vehicle_id = ? 
                  AND status != 'cancelled' 
                  AND ((start_date <= ? AND end_date >= ?) OR (start_date <= ? AND end_date >= ?))";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("issss", $vehicle_id, $end_date, $start_date, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "Sorry, this vehicle is not available for the selected dates.";
        $messageType = "error";
    } else {
        // Create Booking
        $ins_sql = "INSERT INTO bookings (user_id, vehicle_id, start_date, end_date, total_price, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt2 = $conn->prepare($ins_sql);
        $stmt2->bind_param("iissd", $user_id, $vehicle_id, $start_date, $end_date, $total_price);
        
        if ($stmt2->execute()) {
            $message = "Booking successful! Your request is pending approval.";
            $messageType = "success";
        } else {
            $message = "Error creating booking. Please try again.";
            $messageType = "error";
        }
        $stmt2->close();
    }
    $stmt->close();
}

$vehicle_id = isset($_GET['id']) ? $_GET['id'] : 0;
?>

<!-- Main Content -->
<div class="content-container">
    <div class="form-container">
        <div class="form-header">Book Your Ride</div>
        
        <?php if ($message): ?>
            <div style="text-align:center; padding: 10px; margin-bottom: 20px; border-radius: 5px; background-color: <?php echo ($messageType == 'success') ? '#d4edda' : '#f8d7da'; ?>; color: <?php echo ($messageType == 'success') ? '#155724' : '#721c24'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="rent-now">
            <img id="bike-image" src="" alt="Bike Image" style="display: none;">
            <h3 id="bike-name">Select a bike from our Services page</h3>
            <p id="bike-price"></p>
        </div>
        
        <form id="booking-form" method="POST" action="">
            <input type="hidden" name="vehicle_id" id="vehicle_id" value="<?php echo $vehicle_id; ?>">
            <input type="hidden" name="price_per_day" id="price_per_day" value="">
            
            <input id="search-box" type="text" placeholder="Select Location (Mock)" readonly>
            <input type="date" name="pickup_date" id="pickup-date" placeholder="Pick-up Date" required min="<?php echo date('Y-m-d'); ?>">
            <input type="date" name="return_date" id="return-date" placeholder="Return Date" required min="<?php echo date('Y-m-d'); ?>">
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['user']['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            <button type="submit">Confirm & Book</button>
        </form>
    </div>
</div>

<script>
    // Fetch query parameters from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const bikeName = urlParams.get('name');
    const bikePrice = urlParams.get('price');
    const bikeImage = urlParams.get('image');

    // Populate the rent-now section
    if (bikeName && bikePrice && bikeImage) {
        document.getElementById('bike-name').textContent = bikeName;
        document.getElementById('bike-price').textContent = `Price: ₹${bikePrice} per day`;
        document.getElementById('price_per_day').value = bikePrice;
        
        const img = document.getElementById('bike-image');
        img.src = bikeImage; 
        if (bikeImage.startsWith('assets/')) {
            img.src = '../../' + bikeImage;
        }
        img.style.display = 'inline-block';
    } else if (!urlParams.get('id')) {
        document.getElementById('bike-name').textContent = 'No bike selected. Go to Services to pick one!';
        document.querySelector('button[type="submit"]').disabled = true;
    }
</script>

<?php include '../../includes/footer.php'; ?>
