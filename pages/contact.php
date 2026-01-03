<?php
$page_title = "Contact Us - Ride-ease";
$base_path = '..';
$active_page = "contact"; // Note: 'contact' is not in the main nav, so no active highlight
$extra_css = '<link rel="stylesheet" href="../assets/css/contact.css">';
include '../includes/header.php';
?>

<section class="contact-page">
    <h1>Contact Us</h1>
    <p>If you have any questions, feel free to reach out. We are here to assist you!</p>
    <div class="contact-container">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            include '../includes/csrf.php';
            verifyCSRFToken($_POST['csrf_token']);
            include '../config/db.php';
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $message = htmlspecialchars($_POST['message']);

            try {
                $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
                $stmt->execute([$name, $email, $message]);
                echo "<p style='color: green; text-align: center; font-weight: bold;'>Message sent successfully! We will get back to you soon.</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red; text-align: center;'>Error sending message. Please try again.</p>";
            }
        }
        ?>
        <form class="contact-form" action="" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="Your Name" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Your Email" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>

    <div class="contact-details">
        <h2>Our Contact Details</h2>
        <p><strong>Phone:</strong> +91 98765 43210</p>
        <p><strong>Email:</strong> <a href="mailto:support@ride-ease.com">support@ride-ease.com</a></p>
        <p><strong>Address:</strong> 123 Ride-ease Street, New Delhi, India</p>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
