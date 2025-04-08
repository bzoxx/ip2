<?php 
require_once "checklogin.php";

// Initialize variables
$username = $email = $birthdate = $location = $bio = '';
$age = '';

// Fetch user data from database
try {
    $query = "SELECT username, email, birthdate, location, preferences AS bio FROM users WHERE id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $username = htmlspecialchars($user['username']);
        $email = htmlspecialchars($user['email']);
        $birthdate = htmlspecialchars($user['birthdate']);
        $location = htmlspecialchars($user['location']);
        $bio = htmlspecialchars($user['bio']);
        
        // Calculate age from birthdate
        if (!empty($birthdate)) {
            $birthDate = new DateTime($birthdate);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y;
        }
    }
} catch(PDOException $e) {
    error_log("Error fetching user data: " . $e->getMessage());
    $_SESSION['error'] = "Error loading profile data. Please try again.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $newAge = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT, 
            ['options' => ['min_range' => 18, 'max_range' => 120]]);
        $newLocation = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $newBio = filter_input(INPUT_POST, 'bio', FILTER_SANITIZE_STRING);
        
        // Validate inputs
        if ($newAge === false) {
            throw new Exception("Please enter a valid age (18-120)");
        }
        
        // Calculate new birthdate based on age
        $newBirthdate = (new DateTime())->sub(new DateInterval("P{$newAge}Y"))->format('Y-m-d');
        
        // Update database
        $query = "UPDATE users SET 
                  birthdate = :birthdate, 
                  location = :location, 
                  preferences = :bio 
                  WHERE id = :user_id";
        $stmt = $connect->prepare($query);
        $stmt->bindParam(':birthdate', $newBirthdate);
        $stmt->bindParam(':location', $newLocation);
        $stmt->bindParam(':bio', $newBio);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->execute();
        
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
        
    } catch(Exception $e) {
        error_log("Error updating profile: " . $e->getMessage());
        $_SESSION['error'] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Blind Cupid</title>
    <link rel="stylesheet" href="css/profile.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <style>
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #e91e63;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #c2185b;
        }
    </style>
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
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" value="<?= $username ?>" disabled>
            
            <label for="email">Email:</label>
            <input type="email" id="email" value="<?= $email ?>" disabled>
            
            <label for="age">Age:</label>
            <input type="number" id="age" name="age" value="<?= $age ?>" min="18" max="120" required placeholder="<?= $age ?>">
            
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?= $location ?>" placeholder="<?= $location ?>">
            
            <label for="bio">Bio:</label>
            <textarea id="bio" name="bio" placeholder="<?= $bio ?>"><?= $bio ?></textarea>
            
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
