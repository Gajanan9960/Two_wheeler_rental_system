// Dummy authentication check login
function eventHandler(event) {
  event.preventDefault(); // Prevent default form submission

  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;

  // Basic validation
  if (email === '' || password === '') {
      alert('Please fill in all fields.');
      return;
  }

  // Dummy authentication check
  if (email === 'gajanan@gmail.com' && password === 'rental123') {
      alert('Login successful!');
      // Redirect to dashboard or home page
      window.location.href = 'dashboard.html';
  } else {
      alert('Invalid email or password. Please try again.');
  }
}

// Assuming you have a form with an ID of 'loginForm'
const form = document.getElementById('loginForm');
form.addEventListener('submit', eventHandler);