<?php
$page_title = "Bike Rental Reviews - Ride-ease";
$base_path = '..';
$active_page = "reviews";
$extra_css = '<link rel="stylesheet" href="../assets/css/reviews.css">';
include '../includes/header.php';
?>

<h1 class="reviews-title">User Reviews</h1>

<div class="review-container">
    <div class="review">
        <h3>Aditi Mane</h3>
        <p>⭐⭐⭐⭐⭐</p>
        <p>I had an amazing experience renting a bike! The service was excellent and the bike was in great condition. Highly recommend!</p>
    </div>
    <div class="review">
        <h3>Yash Atre</h3>
        <p>⭐⭐⭐⭐⭐</p>
        <p>The bike was perfect for my weekend trip. Easy to ride and very comfortable. Will definitely rent again!</p>
    </div>
    <div class="review">
        <h3>Saurabh Kulkarni</h3>
        <p>⭐⭐⭐⭐⭐</p>
        <p>Fantastic service! The staff was very helpful and the bike was top-notch. I had a wonderful time exploring the city.</p>
    </div>

    <div class="review-form">
        <h2>Leave a Review</h2>
        <form action="#" method="post">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="text" name="rating" placeholder="Rating (1-5)" required>
            <textarea name="review" rows="4" placeholder="Write your review here..." required></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
