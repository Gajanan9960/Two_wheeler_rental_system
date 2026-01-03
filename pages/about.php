<?php
$page_title = "About Us - Ride-ease";
$base_path = '..';
$active_page = "about";
$extra_css = '<link rel="stylesheet" href="../assets/css/about.css">';
include '../includes/header.php';
?>

<!-- Hero Section -->
<section class="about-hero">
    <div class="hero-content">
        <h1>About Ride-ease</h1>
        <p>Driving the future of sustainable mobility.</p>
    </div>
</section>

<div class="about-container">
    <!-- Mission & Story Grid -->
    <div class="about-grid">
        <div class="grid-item text-block">
            <h2>Our Mission</h2>
            <p>At Ride-ease, our mission is to revolutionize transportation by providing affordable, convenient, and eco-friendly two-wheeler rental solutions. We aim to empower individuals with mobility freedom while contributing to a sustainable future.</p>
        </div>
        <div class="grid-item text-block">
            <h2>Our Story</h2>
            <p>Founded in 2020, Ride-ease started as a small initiative to provide hassle-free bike rentals in urban areas. With a vision to address the growing need for quick and affordable transportation, we now operate in multiple cities across the country, serving thousands of satisfied customers.</p>
        </div>
    </div>

    <!-- Values Section -->
    <h2 class="section-title">Why Choose Us?</h2>
    <div class="values-grid">
        <div class="value-card">
            <div class="icon">🛡️</div>
            <h3>Reliable</h3>
            <p>Every ride serves as a promise of safety and dependability.</p>
        </div>
        <div class="value-card">
            <div class="icon">💰</div>
            <h3>Affordable</h3>
            <p>Cost-effective rental options designed for everyone's budget.</p>
        </div>
        <div class="value-card">
            <div class="icon">🚀</div>
            <h3>Innovative</h3>
            <p>Seamless technology to enhance your booking experience.</p>
        </div>
        <div class="value-card">
            <div class="icon">❤️</div>
            <h3>Customer First</h3>
            <p>Your needs drive our innovation and service.</p>
        </div>
    </div>

    <!-- Team Section Removed -->

    <div class="contact-cta">
        <p>Have questions? We'd love to hear from you.</p>
        <a href="contact.php" class="contact-btn">Get in Touch</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
