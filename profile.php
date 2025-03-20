<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Blind Cupid</title>
    <link rel="stylesheet" href="css/profile.css">
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
                <li><a href="dashboard.php" aria-label="Go to Dashboard">Dashboard</a></li>
                <li><a href="profile.php" aria-label="View your Profile">Profile</a></li>
                <li><a href="settings.php" aria-label="Go to Settings">Settings</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <h1>Your Profile</h1>
        <form>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="[Username]" disabled>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="[Age]">
            
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" Placeholder="[Location]">
            
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio">[User's bio]</textarea>
            
            <button type="submit">Update Profile</button>
        </form>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
    </footer>

</body>
</html>
