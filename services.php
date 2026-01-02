<?php
$page_title = "Services - Ride-ease";
$active_page = "services";
$extra_css = '<link rel="stylesheet" href="assets/css/services.css">';
include 'includes/header.php';
?>

<section class="services" id="services">
    <div class="heading">
        <span>Best Services</span>
        <h1>Explore Our Top Deals</h1>
    </div>
        <?php
        include 'config/db.php';
        $result = $conn->query("SELECT * FROM vehicles WHERE status='available'");
        
        // Group by category for better UI organization if needed, or just list all
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Ensure image path is correct. DB has 'assets/img/...'.
                // services.php is in root, so path is fine.
                echo '<div class="box">';
                echo '    <div class="box-img">';
                echo '        <img src="' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                echo '    </div>';
                echo '    <h3>' . htmlspecialchars($row['name']) . '</h3>';
                echo '    <h2>Rs. ' . htmlspecialchars($row['price_per_day']) . '<span>/day</span></h2>';
                echo '    <a href="modules/bookings/rent.php?id=' . $row['id'] . '&name=' . urlencode($row['name']) . '&price=' . $row['price_per_day'] . '&image=' . urlencode($row['image']) . '" class="btn">Rent Now</a>';
                echo '</div>';
            }
        } else {
            echo '<p style="text-align:center; width:100%;">No vehicles available at the moment.</p>';
        }
        $conn->close();
        ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
