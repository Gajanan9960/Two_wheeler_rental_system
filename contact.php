<?php
$page_title = "Contact Us - Ride-ease";
$active_page = "contact"; // Note: 'contact' is not in the main nav, so no active highlight
$extra_css = '<link rel="stylesheet" href="assets/css/contact.css">';
include 'includes/header.php';
?>

<section class="contact-page">
    <h1>Contact Us</h1>
    <p>If you have any questions, feel free to reach out. We are here to assist you!</p>
    <div class="contact-container">
        <form class="contact-form" action="#" method="post">
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

<?php include 'includes/footer.php'; ?>
