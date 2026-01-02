<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($page_title)) {
    $page_title = 'Ride-ease: Two-Wheeler Rental System';
}
if (!isset($base_path)) {
    $base_path = '.';
}
if (!isset($active_page)) {
    $active_page = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="<?php echo $base_path; ?>/assets/css/styles.css">
    <?php if (isset($extra_css)) echo $extra_css; ?>
</head>

<body>
    <header>
        <a href="<?php echo $base_path; ?>/index.php" class="logo">
            <img src="<?php echo $base_path; ?>/assets/img/logo-search-grid-2x.png" alt="Ride-ease Logo">
        </a>
        <ul class="navbar">
            <li><a href="<?php echo $base_path; ?>/index.php" class="<?php echo ($active_page == 'home') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="<?php echo $base_path; ?>/pages/ride.php" class="<?php echo ($active_page == 'ride') ? 'active' : ''; ?>">Ride</a></li>
            <li><a href="<?php echo $base_path; ?>/pages/services.php" class="<?php echo ($active_page == 'services') ? 'active' : ''; ?>">Services</a></li>
            <li><a href="<?php echo $base_path; ?>/pages/about.php" class="<?php echo ($active_page == 'about') ? 'active' : ''; ?>">About</a></li>
            <li><a href="<?php echo $base_path; ?>/pages/reviews.php" class="<?php echo ($active_page == 'reviews') ? 'active' : ''; ?>">Reviews</a></li>
        </ul>
        <div class="header-btn">
            <?php if (isset($_SESSION['user'])): ?>
                <?php if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin'): ?>
                    <a href="<?php echo $base_path; ?>/pages/admin/dashboard.php" class="sign-up" style="margin-right: 10px; text-decoration: none; color: #333;">Admin Panel</a>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>/pages/user/dashboard.php" class="sign-up" style="margin-right: 10px; text-decoration: none; color: #333;">My Dashboard</a>
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>/pages/auth/logout.php" class="sign-in" style="text-decoration: none; color: #333;">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>/pages/auth/signup.php" class="sign-up" style="margin-right: 10px; text-decoration: none; color: #333;">Sign-up</a>
                <a href="<?php echo $base_path; ?>/pages/auth/login.php" class="sign-in" style="text-decoration: none; color: #333;">Sign-in</a>
            <?php endif; ?>
        </div>
    </header>
