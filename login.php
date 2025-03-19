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
        <h1>Login</h1>
<form>
            <label for="email">Email:</label>
            <input id="email" name="email" type="text" placeholder="Enter your email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>

            <button onclick="location.href='interest.php'" type="button" >Login</button>
        </form>
        <p>Don't have an account? <a href="signup.php">Sign up here</a>.</p>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>

</body>
</html>
<!--body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
}
main {
    flex: 1;
} -->
