    <!-- Footer -->
    <footer style="background-color: #333; color: white; padding: 20px 0; text-align: center; margin-top: 50px;">
        <div class="footer-content" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <p>&copy; <?php echo date("Y"); ?> Ride-ease. All rights reserved.</p>

        </div>
    </footer>
    
    <script src="<?php echo $base_path; ?>/assets/js/script.js"></script>
    <?php if (isset($extra_js)) echo $extra_js; ?>
</body>
</html>
