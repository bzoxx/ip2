<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Blind Cupid</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="icon" type="image/png" href="images/logo.png">

</head>
<body>

    <!-- Header -->
    <header>
        <div class="logo">
            <h1>Blind Cupid</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php" aria-label="Go to Home">Home</a></li>
                <li><a href="signup.php" aria-label="Sign Up">Sign Up</a></li>
                <li><a href="login.php" aria-label="Login">Login</a></li>
                <li><a href="about.php" aria-label="Learn About Us">About</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <h1>Sign Up</h1>
        <form id="signup-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
            <div id="username-error" class="error-popup">Username is required and must be at least 3 characters long.</div>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            <div id="email-error" class="error-popup">Please enter a valid email address.</div>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
            <div id="password-error" class="error-popup">Password must be at least 6 characters long.</div>

            <label for="preferences">Few things about yourself:</label>
            <textarea id="preferences" name="preferences" rows="4" placeholder="Tell us what you're looking for"></textarea>
           <div id="preferences-error" class="error-popup">Password must be at least 6 characters long.</div>

            <label for="birthdate">Birth Date:</label>
            <input type="date" id="birthdate" name="birthdate" required>
<div id="birthdate-error" class="error-popup">Birth date is required.</div>

            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </main>
<div class="overlay" id="overlay"></div>
    <div class="popup" id="popup">
        <h2>Error</h2>
        <p id="popup-message">Please fill in all required fields correctly.</p>
        <button id="close-popup">Close</button>
    </div>

<script>
      const form = document.getElementById('signup-form');
const username = document.getElementById('username');
const email = document.getElementById('email');
const password = document.getElementById('password');
const birthdate = document.getElementById('birthdate');

const usernameError = document.getElementById('username-error');
const emailError = document.getElementById('email-error');
const passwordError = document.getElementById('password-error');
const birthdateError = document.getElementById('birthdate-error');

const popup = document.getElementById('popup');
const popupMessage = document.getElementById('popup-message');
const closePopup = document.getElementById('close-popup');
const overlay = document.getElementById('overlay');

function showPopup(message) {
    console.log("Showing popup with message:", message); // Debugging
    popupMessage.textContent = message;
    popup.classList.add('active');
    overlay.classList.add('active');
}

closePopup.addEventListener('click', () => {
    console.log("Closing popup"); // Debugging
    popup.classList.remove('active');
    overlay.classList.remove('active');
});

form.addEventListener('submit', (event) => {
    event.preventDefault(); // Prevent default form submission

    let isValid = true;

    // Validate username
    if (username.value.trim().length < 3) {
        usernameError.classList.add('active');
        isValid = false;
    } else {
        usernameError.classList.remove('active');
    }

    // Validate email
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email.value.trim())) {
        emailError.classList.add('active');
        isValid = false;
    } else {
        emailError.classList.remove('active');
    }

    // Validate password
    if (password.value.trim().length < 6) {
        passwordError.classList.add('active');
        isValid = false;
    } else {
        passwordError.classList.remove('active');
    }

    // Validate birthdate
    if (!birthdate.value) {
        birthdateError.classList.add('active');
        isValid = false;
    } else {
        birthdateError.classList.remove('active');
    }

    // Show popup if not valid
    if (!isValid) {
        showPopup("Please fill in all required fields correctly.");
    } else {
        // If valid, redirect to login.php
        console.log("Form is valid, redirecting to login.php"); // Debugging
        window.location.href = 'login.php';
    }
});

    </script>
    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>

</body>
</html>
