<?php
session_start();
include '../../config/db.php';
include '../../includes/csrf.php';

// Ensure user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$message = "";
$messageType = "";

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    verifyCSRFToken($_POST['csrf_token']);
    $username = htmlspecialchars($_POST['username']);
    $phone = htmlspecialchars($_POST['phone']);

    try {
        $stmt = $conn->prepare("UPDATE users SET username = ?, phone = ? WHERE id = ?");
        $stmt->execute([$username, $phone, $user_id]);
        
        // Update session
        $_SESSION['user']['name'] = $username;
        $_SESSION['user']['phone'] = $phone; // We might want to store phone in session now
        
        $message = "Profile updated successfully!";
        $messageType = "success";
    } catch (PDOException $e) {
        $message = "Error updating profile.";
        $messageType = "error";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    verifyCSRFToken($_POST['csrf_token']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
        $messageType = "error";
    } else {
        // Verify current password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->execute([$hashed_password, $user_id]);
            $message = "Password changed successfully!";
            $messageType = "success";
        } else {
            $message = "Incorrect current password.";
            $messageType = "error";
        }
    }
}

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch(PDO::FETCH_ASSOC);

$page_title = "My Profile - Ride-ease";
$base_path = "../../";
include '../../includes/header.php';
?>

<div class="content-container">
    <h1>My Profile</h1>
    
    <?php if ($message): ?>
        <div style="padding: 10px; margin-bottom: 20px; border-radius: 5px; text-align: center; 
            background-color: <?php echo ($messageType == 'success') ? '#d4edda' : '#f8d7da'; ?>; 
            color: <?php echo ($messageType == 'success') ? '#155724' : '#721c24'; ?>;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <!-- Edit Profile Form -->
        <div style="flex: 1; min-width: 300px;">
            <h2>Edit Details</h2>
            <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <label style="display:block; margin-bottom: 5px; font-weight: bold;">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($current_user['username']); ?>" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                <label style="display:block; margin-bottom: 5px; font-weight: bold;">Email</label>
                <input type="email" value="<?php echo htmlspecialchars($current_user['email']); ?>" readonly style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; background: #eee; cursor: not-allowed;">

                <label style="display:block; margin-bottom: 5px; font-weight: bold;">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($current_user['phone'] ?? ''); ?>" placeholder="Enter phone number" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                <button type="submit" name="update_profile" style="background: #fe5b3d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Update Profile</button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div style="flex: 1; min-width: 300px;">
            <h2>Change Password</h2>
            <form method="POST" action="" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <label style="display:block; margin-bottom: 5px; font-weight: bold;">Current Password</label>
                <input type="password" name="current_password" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                <label style="display:block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                <input type="password" name="new_password" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                <label style="display:block; margin-bottom: 5px; font-weight: bold;">Confirm New Password</label>
                <input type="password" name="confirm_password" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px;">

                <button type="submit" name="change_password" style="background: #333; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Change Password</button>
            </form>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <a href="dashboard.php" style="color: #666; text-decoration: none;">&larr; Back to Dashboard</a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
