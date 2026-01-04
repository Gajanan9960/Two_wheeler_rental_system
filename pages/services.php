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
            // Fetch all vehicles (available and rented) to list catalog, but check status for count
            $stmt = $conn->query("SELECT * FROM vehicles"); // Fetch ALL to show catalog even if out of stock
            $all_vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group by Name
            $grouped = [];
            foreach ($all_vehicles as $v) {
                $name = $v['name'];
                if (!isset($grouped[$name])) {
                    $grouped[$name] = [
                        'details' => $v, // Store first instance for details (image, price, etc)
                        'available_count' => 0,
                        'rent_id' => null // ID to use for booking
                    ];
                }
                
                if ($v['status'] === 'available') {
                    $grouped[$name]['available_count']++;
                    // Pick the first available ID for the link
                    if ($grouped[$name]['rent_id'] === null) {
                        $grouped[$name]['rent_id'] = $v['id'];
                    }
                }
            }
            
            // Sort groups by price
            uasort($grouped, function($a, $b) {
                return $a['details']['price_per_day'] <=> $b['details']['price_per_day'];
            });
            
            $affordable = [];
            $premium = [];
            $electric = [];
            
            foreach ($grouped as $name => $data) {
                $v = $data['details'];
                if ($v['category'] === 'ebike') {
                    $electric[] = $data;
                } elseif ($v['price_per_day'] < 1000) {
                    $affordable[] = $data;
                } else {
                    $premium[] = $data;
                }
            }
            
            $groups = [
                'Affordable Rides' => $affordable,
                'Premium Bikes' => $premium,
                'Electric & Eco-Friendly' => $electric
            ];
            
            foreach ($groups as $title => $list) {
                if (!empty($list)) {
                    echo '<h2 style="text-align:center; margin: 40px 0 20px; color: #fe5b3d; font-size: 2rem;">' . htmlspecialchars($title) . '</h2>';
                    echo '<div class="services-container">';
                    foreach($list as $item) {
                        $row = $item['details'];
                        $count = $item['available_count'];
                        $rentId = $item['rent_id'];
                        
                        echo '<div class="box">';
                        echo '    <div class="box-img">';
                        $imgPath = $row['image']; 
                        if (strpos($imgPath, 'assets/') === 0) {
                            $imgPath = '../' . $imgPath;
                        }
                        echo '        <img src="' . htmlspecialchars($imgPath) . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '    </div>';
                        echo '    <div style="padding: 10px;">';
                        echo '        <h3>' . htmlspecialchars($row['name']) . '</h3>';
                        echo '        <h2>Rs. ' . htmlspecialchars($row['price_per_day']) . '<span>/day</span></h2>';
                        
                        // Availability Badge
                        // Availability Badge
                        $badgeClass = $count > 0 ? 'in-stock' : 'out-of-stock';
                        echo '        <span class="availability-badge ' . $badgeClass . '">';
                        echo            $count > 0 ? "$count Available" : "Fully Booked";
                        echo '        </span>';

                         if ($count > 0) {
                            echo '        <a href="bookings/bookings.php?vehicle_id=' . $rentId . '" class="btn">Rent Now</a>';
                        } else {
                            echo '        <button class="btn" style="background: #ccc; cursor: not-allowed;" disabled>Out of Stock</button>';
                        }
                        echo '    </div>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            
            if (empty($grouped)) {
                echo '<p style="text-align:center; width:100%;">No vehicles in fleet.</p>';
            }

        } catch (PDOException $e) {
             echo '<p>Error loading vehicles.</p>';
             // echo $e->getMessage(); // Debug
        }
        ?>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
