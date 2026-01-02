// main.js

// Function to toggle the mobile menu
const menuIcon = document.getElementById('menu-icon');
const navbar = document.querySelector('.navbar');

menuIcon.addEventListener('click', () => {
    navbar.classList.toggle('active'); // Toggle the active class
});

// Function to adjust styles based on window size
function adjustLayout() {
    const navbar = document.querySelector('.navbar');
    if (window.innerWidth < 768) { // Example breakpoint for mobile
        navbar.style.flexDirection = 'column'; // Stack items vertically
    } else {
        navbar.style.flexDirection = 'row'; // Stack items horizontally
    }
}

// Event listener for window resize
window.addEventListener('resize', adjustLayout);

// Initial call to set layout on page load
adjustLayout();