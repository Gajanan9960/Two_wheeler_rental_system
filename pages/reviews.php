<?php
$page_title = "Bike Rental Reviews - Ride-ease";
$base_path = '..';
$active_page = "reviews";
$extra_css = '<link rel="stylesheet" href="../assets/css/reviews.css">';
include '../includes/header.php';
include '../config/db.php';
?>

<h1 class="reviews-title">User Reviews</h1>

<div class="review-container">
    <?php
    // Handle Review Submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        include '../includes/csrf.php';
        verifyCSRFToken($_POST['csrf_token']);
        if (isset($_SESSION['user'])) {
            $user_id = $_SESSION['user']['id'];
            $rating = $_POST['rating'];
            $comment = $_POST['review'];
            
            try {
                $stmt = $conn->prepare("INSERT INTO reviews (user_id, rating, comment) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $rating, $comment]);
                echo "<p style='color: green; text-align: center;'>Review submitted successfully!</p>";
            } catch (PDOException $e) {
                echo "<p style='color: red; text-align: center;'>Error submitting review.</p>";
            }
        } else {
             echo "<p style='color: red; text-align: center;'>You must be logged in to leave a review.</p>";
        }
    }
    
    // Fetch Reviews
    $stmt = $conn->query("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($reviews) > 0) {
        foreach ($reviews as $rev) {
            $stars = str_repeat('⭐', $rev['rating']);
            echo '<div class="review">';
            echo '<h3>' . htmlspecialchars($rev['username']) . '</h3>';
            echo '<p>' . $stars . '</p>';
            echo '<p>' . htmlspecialchars($rev['comment']) . '</p>';
            echo '<small style="color: #666; font-size: 0.8rem;">' . $rev['created_at'] . '</small>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align:center;">No reviews yet. Be the first to review!</p>';
    }
    ?>

    <div class="review-form">
        <h2>Leave a Review</h2>
        <?php if(isset($_SESSION['user'])): ?>
        <form action="#" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['user']['name']); ?>" readonly style="background-color: #f0f0f0;">
            <select name="rating" required style="width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;">
                <option value="" disabled selected>Select Rating</option>
                <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                <option value="4">⭐⭐⭐⭐ (Good)</option>
                <option value="3">⭐⭐⭐ (Average)</option>
                <option value="2">⭐⭐ (Poor)</option>
                <option value="1">⭐ (Terrible)</option>
            </select>
            <textarea name="review" rows="4" placeholder="Write your review here..." required></textarea>
            <button type="submit">Submit Review</button>
        </form>
        <?php else: ?>
            <p style="text-align: center; padding: 20px;"><a href="../pages/auth/login.php" style="color: #fe5b3d; font-weight: bold;">Login</a> to leave a review.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
