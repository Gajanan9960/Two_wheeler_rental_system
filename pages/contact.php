<?php
$page_title = "Contact Us - Ride-ease";
$base_path = '..';
$active_page = "contact";
$extra_css = '<link rel="stylesheet" href="../assets/css/contact.css">';
include '../includes/header.php';
include_once '../includes/csrf.php';
?>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="hero-content">
        <h1>Get in Touch</h1>
        <p>We'd love to hear from you. Here's how you can reach us.</p>
    </div>
</section>

<div class="contact-wrapper">
    <div class="contact-grid">
        <!-- Contact Info Side -->
        <div class="contact-info">
            <h2>Contact Information</h2>
            <p class="info-desc">Fill up the form and our team will get back to you within 24 hours.</p>
            
            <div class="info-item">
                <div class="icon">📞</div>
                <div class="details">
                    <h3>Phone</h3>
                    <p>+91 98765 43210</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon">📧</div>
                <div class="details">
                    <h3>Email</h3>
                    <p>support@ride-ease.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="icon">📍</div>
                <div class="details">
                    <h3>Location</h3>
                    <p>123 Ride-ease Street, New Delhi, India</p>
                </div>
            </div>

            <!-- Social Links (Mock) -->
            <div class="social-links">
                <a href="#">FB</a>
                <a href="#">TW</a>
                <a href="#">IG</a>
                <a href="#">LI</a>
            </div>
        </div>

        <!-- Contact Form Side -->
        <div class="contact-form-container">
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 // CSRF verified above (included at top)
verifyCSRFToken($_POST['csrf_token']);
                include '../config/db.php';
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);

                try {
                    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $message]);
                    echo "<div class='alert success'>Message sent successfully!</div>";
                } catch (PDOException $e) {
                    echo "<div class='alert error'>Error sending message. Please try again.</div>";
                }
            }
            ?>
            
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="send-btn">Send Message</button>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
