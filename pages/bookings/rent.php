<?php
session_start();
include '../../config/db.php';
include '../../includes/mailer.php';

$page_title = "Book Your Ride - Ride-ease";
$base_path = "../../";
// Using the Modern CSS
$extra_css = '<link rel="stylesheet" href="../../assets/css/booking_modern.css">';
include '../../includes/header.php';

$message = "";
$messageType = "";

// ---------------------------------------------------------
// 1. Authorization Check
// ---------------------------------------------------------
if (!isset($_SESSION['user'])) {
    // Redirect back here after login
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    echo "<script>window.location.href='../auth/login.php?redirect=$redirect';</script>";
    exit();
}

$user = $_SESSION['user'];
$user_email = $user['email'] ?? '';
$user_name = $user['name'] ?? '';

// Fetch User ID
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->execute([$user_email]);
$user_id = $stmt->fetch(PDO::FETCH_COLUMN);

// ---------------------------------------------------------
// 2. Identify Vehicle (Securely)
// ---------------------------------------------------------
$vehicle_id = 0;
// Prioritize POST if submitting, otherwise GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = intval($_POST['vehicle_id']);
} elseif (isset($_REQUEST['id'])) {
    $vehicle_id = intval($_REQUEST['id']);
}

// Fetch Vehicle Data from Database (Ignore URL Params for details)
$vehicle = null;
if ($vehicle_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
        $stmt->execute([$vehicle_id]);
        $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error loading vehicle details.";
        $messageType = "error";
    }
}

// ---------------------------------------------------------
// 3. Handle Booking Submission
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../includes/csrf.php';
    if (!isset($_POST['csrf_token'])) {
         die("CSRF Token missing"); // Fail safely
    }
    verifyCSRFToken($_POST['csrf_token']);
    
    // Collect Inputs
    $start_date = $_POST['pickup_date'];
    $end_date = $_POST['return_date'];
    $phone = $_POST['phone'];
    $driver_age = intval($_POST['driver_age']);
    $license_no = strtoupper(trim($_POST['license_no']));
    $pickup_loc = $_POST['pickup_location'];
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    
    // Server-Side Validations
    $today = date('Y-m-d');
    if ($start_date < $today) {
        $message = "Pickup date cannot be in the past.";
        $messageType = "error";
    } elseif ($end_date < $start_date) {
        $message = "Return date must be equal to or after pickup date.";
        $messageType = "error";
    } elseif ($driver_age < 18) {
        $message = "Driver must be at least 18 years old.";
        $messageType = "error";
    } elseif (empty($license_no) || strlen($license_no) < 5) {
        $message = "Please provide a valid Driving License Number.";
        $messageType = "error";
    } elseif (empty($pickup_loc)) {
        $message = "Please select a Pickup Location.";
        $messageType = "error";
    } elseif (!$vehicle) {
        $message = "Invalid or unavailable vehicle selected.";
        $messageType = "error";
    } else {
        // Validation Passed: Process Booking
        
        // 1. Calculate Price Securely (Server-Side)
        $price_per_day = $vehicle['price_per_day'];
        $diff = strtotime($end_date) - strtotime($start_date);
        $days = ceil($diff / (60 * 60 * 24));
        if ($days < 1) $days = 1;
        $total_price = $days * $price_per_day;

        // 2. Database Availability Check (Concurrency Safe)
        try {
            $check_sql = "SELECT 1 FROM bookings 
                          WHERE vehicle_id = ? 
                          AND status != 'cancelled' 
                          AND ((start_date <= ? AND end_date >= ?) OR (start_date <= ? AND end_date >= ?))";
            $stmt = $conn->prepare($check_sql);
            $stmt->execute([$vehicle_id, $end_date, $start_date, $start_date, $end_date]);

            if ($stmt->fetch()) {
                $message = "Sorry, this vehicle is already booked for those dates.";
                $messageType = "error";
            } else {
                // 3. Create Booking Record
                // Status Lifecycle starts at 'pending'
                $ins_sql = "INSERT INTO bookings (
                    user_id, vehicle_id, start_date, end_date, total_price, status, 
                    pickup_location, driver_age, license_no, payment_method
                ) VALUES (?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?)";
                
                $stmt2 = $conn->prepare($ins_sql);
                $params = [
                    $user_id, 
                    $vehicle_id, 
                    $start_date, 
                    $end_date, 
                    $total_price, 
                    $pickup_loc, 
                    $driver_age, 
                    $license_no, 
                    $payment_method
                ];

                if ($stmt2->execute($params)) {
                    $booking_id = $conn->lastInsertId();
                    
                    // 4. Send Email Notification
                    $emailBody = "Dear $user_name,\n\n"
                        . "Your booking request (#$booking_id) has been received.\n"
                        . "--------------------------------\n"
                        . "Vehicle: {$vehicle['name']}\n"
                        . "Pickup: $pickup_loc\n"
                        . "Dates: $start_date to $end_date\n"
                        . "Total Price: ₹$total_price\n"
                        . "Payment Method: $payment_method\n"
                        . "--------------------------------\n\n"
                        . "Status: Pending Approval.\n"
                        . "You can track this in your Dashboard.\n\n"
                        . "Ride Safe,\nRide-ease Team";
                    
                    sendEmail($user_email, "Booking Confirmation #$booking_id", $emailBody);

                    $message = "Booking Successful! Redirecting to your dashboard...";
                    $messageType = "success";
                    // Redirect after 2 seconds
                    echo "<script>setTimeout(() => window.location.href='../user/dashboard.php', 2000);</script>";
                } else {
                    $message = "Failed to save booking. Please try again.";
                    $messageType = "error";
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "System error occurred. Please contact support.";
            $messageType = "error";
        }
    }
}
?>

<div class="booking-container">
    <!-- LEFT PANEL: Dynamic Vehicle Details -->
    <div class="booking-summary">
        <?php if ($vehicle): 
            // Fix Image Path
            $img = $vehicle['image'];
            if (strpos($img, 'assets/') === 0) $img = '../../' . $img;
        ?>
            <!-- Vehicle Image -->
            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
            
            <!-- Details -->
            <h2><?php echo htmlspecialchars($vehicle['name']); ?></h2>
            <p class="price-tag">Rate: ₹<span id="rate-display"><?php echo htmlspecialchars($vehicle['price_per_day']); ?></span> / day</p>
            
            <!-- Dynamic Price Calculator -->
            <div class="total-cost-box">
                <h3>Total Estimated Cost</h3>
                <span class="amount" id="total-amount">₹0</span>
                <small id="duration-text">Select dates to calculate</small>
            </div>
            
            <!-- Trust Badges -->
            <div style="margin-top: 30px; text-align: left; background: #f8fafc; padding: 15px; border-radius: 12px;">
                <h4><i class="fas fa-shield-alt" style="color:var(--primary)"></i> Premium Assurance</h4>
                <ul style="padding-left: 20px; font-size: 0.9rem; color: #64748b; margin-top: 10px;">
                    <li>Zero Deposit required</li>
                    <li>24/7 Roadside Assistance</li>
                    <li>Complimentary Helmet</li>
                </ul>
            </div>

        <?php else: ?>
            <!-- Fallback if no ID provided -->
            <div style="padding: 40px; text-align: center;">
                <p>No vehicle selected.</p>
                <div style="margin-top:10px; color:#666; font-size:0.9rem;">
                   <i class="fas fa-info-circle"></i> Please select a bike from our fleet first.
                </div>
                <br>
                <a href="../services.php" class="btn-book" style="display:inline-block; text-align:center; text-decoration:none; width:auto; padding: 12px 30px;">
                    Go to Services
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT PANEL: Booking Form -->
    <div class="booking-form-wrapper">
        <div class="booking-form-header">
            <h1>Secure Checkout</h1>
            <p>Complete your booking details below.</p>
        </div>

        <!-- Success/Error Message Display -->
        <?php if ($message): ?>
            <div style="padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align:center; font-weight:600; 
                background: <?php echo $messageType === 'success' ? '#dcfce7' : '#fee2e2'; ?>; 
                color: <?php echo $messageType === 'success' ? '#166534' : '#991b1b'; ?>;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="bookingForm" <?php echo !$vehicle ? 'style="opacity:0.5; pointer-events:none;"' : ''; ?>>
             <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
             <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>">
             <!-- Hidden inputs for JS calc -->
             <input type="hidden" id="base-price" value="<?php echo $vehicle['price_per_day'] ?? 0; ?>">

             <!-- 1. User Info (Auto-filled + New Fields) -->
             <div class="form-grid">
                 <div class="input-group">
                     <label>Full Name</label>
                     <input type="text" value="<?php echo htmlspecialchars($user_name); ?>" readonly style="background:#f1f5f9; cursor:not-allowed;">
                 </div>
                 <div class="input-group">
                     <label>Email Address</label>
                     <input type="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly style="background:#f1f5f9; cursor:not-allowed;">
                 </div>
                 <div class="input-group">
                     <label>Phone Number <span style="color:red">*</span></label>
                     <input type="tel" name="phone" placeholder="+91 98765 43210" required>
                 </div>
                 <div class="input-group">
                     <label>Driver Age <span style="color:red">*</span></label>
                     <input type="number" name="driver_age" min="18" max="99" placeholder="18+" required>
                 </div>
                 <div class="input-group full-width">
                     <label>Driving License Number <span style="color:red">*</span></label>
                     <input type="text" name="license_no" placeholder="e.g. MH12 20220012345" required style="text-transform:uppercase;">
                 </div>
             </div>

             <!-- 2. Rental Details -->
             <div class="form-grid" style="margin-top: 10px;">
                 <div class="input-group full-width">
                     <label>Pickup Location <span style="color:red">*</span></label>
                     <select name="pickup_location" required>
                         <option value="" disabled selected>Select a Pickup Hub</option>
                         <option value="Main Hub - Center City">Main Hub - Center City</option>
                         <option value="Airport Station">Airport Station</option>
                         <option value="Railway Plaza">Railway Plaza</option>
                         <option value="North Gate Branch">North Gate Branch</option>
                     </select>
                 </div>
                 <div class="input-group">
                     <label>Pickup Date</label>
                     <input type="text" id="pickup_date" name="pickup_date" placeholder="Select Date" required>
                 </div>
                 <div class="input-group">
                     <label>Return Date</label>
                     <input type="text" id="return_date" name="return_date" placeholder="Select Date" required>
                 </div>
             </div>

             <!-- 3. Payment Method -->
             <div class="input-group full-width">
                 <label>Payment Method</label>
                 <div class="payment-methods">
                     <div class="payment-option selected" onclick="selectPayment(this)">
                         <i class="fas fa-credit-card"></i> Card
                     </div>
                     <div class="payment-option" onclick="selectPayment(this)">
                         <i class="fas fa-qrcode"></i> UPI
                     </div>
                     <div class="payment-option" onclick="selectPayment(this)">
                         <i class="fas fa-money-bill-wave"></i> Cash
                     </div>
                 </div>
                 <input type="hidden" name="payment_method" id="payment_method_input" value="Card">
             </div>

             <button type="submit" class="btn-book" <?php echo !$vehicle ? 'disabled' : ''; ?>>
                 Confirm Booking <i class="fas fa-arrow-right" style="margin-left: 10px;"></i>
             </button>
        </form>
    </div>
</div>

<!-- SCRIPTS: Flatpickr & Logic -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
    // 1. Payment Selection Interaction
    function selectPayment(el) {
        // Visual Update
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
        el.classList.add('selected');
        // Hidden Input Update
        const method = el.innerText.trim();
        document.getElementById('payment_method_input').value = method;
    }

    // 2. Main Logic: Dates, Availability, Price
    document.addEventListener('DOMContentLoaded', function() {
        const basePrice = parseFloat(document.getElementById('base-price').value) || 0;
        const totalAmountEl = document.getElementById('total-amount');
        const durationTextEl = document.getElementById('duration-text');
        
        // Only run if we have a valid vehicle
        const bikeId = "<?php echo $vehicle ? $vehicle['id'] : 0; ?>";
        if (bikeId == "0") return;

        let startData = null;
        let endData = null;

        // Fetch Blocked Dates from API
        fetch(`../../api/get_availability.php?vehicle_id=${bikeId}`)
            .then(res => res.json())
            .then(data => {
                const blocked = data.blocked || [];

                // Initialize Pickup Picker
                const startPicker = flatpickr("#pickup_date", {
                    minDate: "today",
                    disable: blocked,
                    dateFormat: "Y-m-d",
                    onChange: (selectedDates) => {
                        startData = selectedDates[0];
                        if(startData) {
                            // Enforce return date after pickup
                            endPicker.set('minDate', startData);
                            // Clear return date if it's invalid
                            if (endData && endData < startData) {
                                endPicker.clear();
                                endData = null;
                            }
                        }
                        updatePrice();
                    }
                });
                
                // Initialize Return Picker
                const endPicker = flatpickr("#return_date", {
                    minDate: "today",
                    disable: blocked,
                    dateFormat: "Y-m-d",
                    onChange: (selectedDates) => {
                        endData = selectedDates[0];
                        updatePrice();
                    }
                });
            })
            .catch(err => console.error("API Error:", err));

        // Price Calculation Logic
        function updatePrice() {
            if (startData && endData && basePrice > 0) {
                const diffTime = endData - startData;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                
                // Minimum rental is 1 day
                const days = diffDays > 0 ? diffDays : 1;
                
                const targetPrice = days * basePrice;
                
                // Update UI
                animateValue(totalAmountEl, targetPrice);
                durationTextEl.textContent = `For ${days} day(s)`;
                durationTextEl.style.color = '#166534';
            } else {
                totalAmountEl.textContent = '₹0';
                durationTextEl.textContent = 'Select dates to calculate';
                durationTextEl.style.color = '#64748b';
            }
        }

        // Fun Animation for Numbers
        function animateValue(obj, end) {
            let startTimestamp = null;
            const duration = 500;
            const startVal = parseInt(obj.textContent.replace('₹', '')) || 0;
            
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                const currentVal = Math.floor(progress * (end - startVal) + startVal);
                obj.textContent = '₹' + currentVal;
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
    });
</script>

<?php include '../../includes/footer.php'; ?>
