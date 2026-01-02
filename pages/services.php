<?php
$page_title = "Services - Ride-ease";
$base_path = '..';
$active_page = "services";
$extra_css = '<link rel="stylesheet" href="../assets/css/services.css">';
include '../includes/header.php';
?>

<section class="services" id="services">
    <div class="heading">
        <span>Best Services</span>
        <h1>Explore Our Top Deals</h1>
    </div>
        <?php
        include '../config/db.php';
        
        try {
            $stmt = $conn->query("SELECT * FROM vehicles WHERE status='available'");
            $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Sort by price low to high
            usort($vehicles, function($a, $b) {
                return $a['price_per_day'] <=> $b['price_per_day'];
            });
            
            $affordable = [];
            $premium = [];
            $electric = [];
            
            foreach ($vehicles as $v) {
                if ($v['category'] === 'ebike') {
                    $electric[] = $v;
                } elseif ($v['price_per_day'] < 1000) {
                    $affordable[] = $v;
                } else {
                    $premium[] = $v;
                }
            }
            
            // Define groups order: Affordable, Premium, Electric
            $groups = [
                'Affordable Rides' => $affordable,
                'Premium Bikes' => $premium,
                'Electric & Eco-Friendly' => $electric
            ];
            
            foreach ($groups as $title => $list) {
                if (!empty($list)) {
                    echo '<h2 style="text-align:center; margin: 40px 0 20px; color: #fe5b3d; font-size: 2rem;">' . htmlspecialchars($title) . '</h2>';
                    echo '<div class="services-container">';
                    foreach($list as $row) {
                        echo '<div class="box">';
                        echo '    <div class="box-img">';
                        $imgPath = $row['image']; 
                        if (strpos($imgPath, 'assets/') === 0) {
                            $imgPath = '../' . $imgPath;
                        }
                        echo '        <img src="' . htmlspecialchars($imgPath) . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '    </div>';
                        echo '    <h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '    <h2>Rs. ' . htmlspecialchars($row['price_per_day']) . '<span>/day</span></h2>';
                        echo '    <a href="../modules/bookings/rent.php?id=' . $row['id'] . '&name=' . urlencode($row['name']) . '&price=' . $row['price_per_day'] . '&image=' . urlencode($row['image']) . '" class="btn">Rent Now</a>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            
            if (empty($vehicles)) {
                echo '<p style="text-align:center; width:100%;">No vehicles available at the moment.</p>';
            }

        } catch (PDOException $e) {
             echo '<p>Error loading vehicles.</p>';
        }
        ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
