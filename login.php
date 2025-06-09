<?php
session_start();
require_once "conn.php"; // uses your existing connection setup

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connect->exec("USE dating_app");

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $connect->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: interest.php");
            exit;
        } else {
            $message = "Invalid username or password.";
        }
    } else {
        $message = "Please fill in both fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blind Cupid</title>
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>

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

<main>
    <h1>Login</h1>

    <?php if (!empty($message)): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
    <label for="username">Username:</label>
    <input id="username" name="username" type="text" placeholder="Enter your username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="Enter your password" required>

    <button type="submit">Login</button>
</form>


    <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</main>

<footer>
    <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
    <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
</footer>

</body>
</html>
