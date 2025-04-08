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

    // Validation rules
    if (strlen($username) < 3) {
        $message = "Invalid form data: Username must be at least 3 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid form data: Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $message = "Invalid form data: Password must be at least 6 characters.";
    } elseif (empty($birthdate)) {
        $message = "Invalid form data: Please select your birth date.";
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
            $stmt = $connect->prepare("INSERT INTO users (username, email, password, preferences, birthdate)
                                       VALUES (:username, :email, :password, :preferences, :birthdate)");

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':preferences', $preferences);
            $stmt->bindParam(':birthdate', $birthdate);

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
        <span>Birth date is <strong>required</strong></span>
    </ol>
</div>


    <form id="signup-form" method="POST" action="signup.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>

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

</body>
</html>
