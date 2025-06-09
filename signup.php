<?php
session_start();
require_once "conn.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect->exec("USE dating_app");

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $preferences = trim($_POST['preferences'] ?? '');
    $birthdate = $_POST['birthdate'] ?? '';
    $gender = ($_POST['gender'] === 'female') ? 0 : 1;  // Convert gender to binary

    // Check if username already exists
    $stmt = $connect->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $usernameExists = $stmt->fetchColumn() > 0;

    if ($usernameExists) {
        $message = "Username is already taken. Please choose a different one.";
    } else {
        // Check if email already exists
        $stmt = $connect->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $message = "Email already exists. Please use a different one.";
        } else {
            // Insert user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $connect->prepare("INSERT INTO users (username, email, password, preferences, birthdate, gender)
                                    VALUES (:username, :email, :password, :preferences, :birthdate, :gender)");

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':preferences', $preferences);
            $stmt->bindParam(':birthdate', $birthdate);
            $stmt->bindParam(':gender', $gender);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Signup failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Blind Cupid</title>
    <link rel="stylesheet" href="css/signup.css">
    <link rel="stylesheet" href="css/validation.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>

<header>
    <div class="logo">
        <h1>Blind Cupid</h1>
    </div>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="signup.php">Sign Up</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="about.php">About</a></li>
        </ul>
    </nav>
</header>

<main>
    <h1>Sign Up</h1>
    <?php if (!empty($message)): ?>
    <p style="color:red; font-weight: bold;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div style="margin-bottom: 1rem; font-size: 0.9em; color: #555;">
        <ol>
            <span>Username must be at least <strong>3 characters</strong></span><br/>
            <span>Password must be at least <strong>6 characters</strong></span><br/>
            <span>Email must be <strong>valid</strong></span><br/>
            <span>Birth date is <strong>required</strong> and you must be at least 18</span>
        </ol>
    </div>

    <form id="signup-form" method="POST" action="signup.php" novalidate>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Choose a username" required>
            <div id="username-status" class="status-message"></div>
        </div>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="">Select gender</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
        </select>

        <label for="preferences">Few things about yourself:</label>
        <textarea id="preferences" name="preferences" rows="4" placeholder="Tell us what you're looking for"></textarea>

        <label for="birthdate">Birth Date:</label>
        <input type="date" id="birthdate" name="birthdate" required>

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</main>

<footer>
    <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
    <p><a href="about.php">About</a> | <a href="privacyPolicy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
</footer>

<script src="js/validation.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const usernameInput = document.getElementById('username');
    const usernameStatus = document.getElementById('username-status');
    const signupForm = document.getElementById('signup-form');
    let typingTimer;
    let isUsernameAvailable = false;

    usernameInput.addEventListener('input', function() {
        clearTimeout(typingTimer);
        const username = this.value.trim();
        
        if (username.length < 3) {
            usernameStatus.textContent = 'Username must be at least 3 characters';
            usernameStatus.className = 'status-message error';
            isUsernameAvailable = false;
            return;
        }

        // Wait for 500ms after user stops typing before checking
        typingTimer = setTimeout(() => {
            fetch(`check_username.php?username=${encodeURIComponent(username)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        usernameStatus.textContent = data.message;
                        usernameStatus.className = 'status-message error';
                        isUsernameAvailable = false;
                    } else {
                        usernameStatus.textContent = data.message;
                        usernameStatus.className = data.available ? 
                            'status-message success' : 'status-message error';
                        isUsernameAvailable = data.available;
                    }
                })
                .catch(error => {
                    usernameStatus.textContent = 'Error checking username';
                    usernameStatus.className = 'status-message error';
                    isUsernameAvailable = false;
                });
        }, 500);
    });

    // Prevent form submission if username is taken
    signupForm.addEventListener('submit', function(e) {
        if (!isUsernameAvailable) {
            e.preventDefault();
            usernameStatus.textContent = 'Please choose an available username before submitting';
            usernameStatus.className = 'status-message error';
            usernameInput.focus();
        }
    });
});
</script>

<style>
.form-group {
    margin-bottom: 1rem;
}

.status-message {
    font-size: 0.9em;
    margin-top: 0.25rem;
    padding: 0.25rem;
    border-radius: 4px;
}

.status-message.success {
    color: #28a745;
    background-color: #d4edda;
}

.status-message.error {
    color: #dc3545;
    background-color: #f8d7da;
}
</style>
</body>
</html>
