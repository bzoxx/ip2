<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Blind Cupid</title>
    <link rel="stylesheet" href="css/setting.css">
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
        
                <li><a href="dashboard.php" aria-label="Go to Dashboard">Dashboard</a></li>
                <li><a href="signup.php" aria-label="Sign Up">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <h1>Your Settings</h1>
        <form>
            <label for="password">Change Password:</label>
            <input type="password" id="password" name="password" placeholder="New password">
            
            <label for="email">Email Notifications:</label>
            <input type="checkbox" id="email" name="email"> Enable email notifications
            
            <label for="notifications">App Notifications:</label>
            <input type="checkbox" id="notifications" name="notifications"> Enable app notifications
            
            <button type="submit">Save Settings</button>
        </form>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
    </footer>

</body>
</html>
