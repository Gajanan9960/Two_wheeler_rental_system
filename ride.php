<?php
$page_title = "Ride - Ride-ease";
$active_page = "ride";
$extra_css = '<link rel="stylesheet" href="assets/css/ride.css">';
include 'includes/header.php';
?>

<section class="ride" id="ride">
    <div class="heading">
        <span>How it works</span>
        <h1>Renting Made Easy in 3 Steps</h1>
    </div>
   
    <div class="ride-container">
        <div class="box">
            <h2>Choose A Location</h2>
            <p>Use our simple search tool to select your preferred pickup location for a hassle-free experience.</p>
        </div>
        <div class="box">
            <h2>Pick-up Date</h2>
            <p>Select a convenient pickup date to start your journey with Ride-ease's trusted two-wheelers.</p>
        </div>
        <div class="box">
            <h2>Return Date</h2>
            <p>Set your return date while booking to ensure a seamless end to your rental experience.</p>
        </div>
    </div>
    
    <div class="info-paragraph">
        <h2>Why Choose Ride-ease?</h2>
        <p>
            At Ride-ease, we pride ourselves on making transportation accessible and efficient. 
            Whether you're commuting to work, exploring new cities, or simply running errands, our 
            reliable two-wheeler rentals ensure convenience and affordability. Experience flexibility 
            like never before, with easy booking, a wide range of bikes, and exceptional customer service.
        </p>
    </div>

    <!-- <div class="form-container">
        <form action="">
            <input id="search-box" type="search" placeholder="Search Places">
            <input type="date" placeholder="Pick-up Date">
            <input type="date" placeholder="Return Date">
            <button type="submit" class="btn">Find Bikes</button>
        </form>
    </div> -->
    
    <!-- Hidden Map -->
    <div id="map-container">
        <div id="map"></div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
