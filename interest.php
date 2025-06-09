<?php 
require_once "checklogin.php"; 

// Check if user has already selected interests
try {
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $query = "SELECT COUNT(*) FROM user_interest WHERE user_id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $count = $stmt->fetchColumn();

    // If user has interests, redirect to dashboard
    if ($count > 0) {
        header("Location: dashboard.php");
        exit();
    }
} catch(PDOException $e) {
    // Log error and continue to show interest selection
    error_log("Error checking user interests: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interest Survey - Blind Cupid</title>
    <link rel="stylesheet" href="css/interest.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>

    <!-- Header Section -->
    <header>
        <div class="logo">
            <h1>Blind Cupid</h1>
        </div>
        <nav>
            <ul>
                <li><a href="dashboard.php" aria-label="Go to Dashboard">Dashboard</a></li>
                <li><a href="profile.php" aria-label="View your Profile">Profile</a></li>
                <li><a href="settings.php" aria-label="Go to Settings">Settings</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Section -->
    <main>
        <h1>Tell Us About Yourself</h1>
        <p class="description">Select your interests to help us match you with compatible individuals.</p>

        <!-- Survey Form -->
        <form action="save_interests.php" method="post">
            <fieldset>
                <legend>Personal Preferences</legend>

                <!-- Interests Selection -->
                <div class="question-group">
                    <label>Select your interests:</label>
                    <div class="interests-selection">
                        <?php
                        try {
                            // Fetch all interests from database
                            $query = "SELECT * FROM interests ORDER BY name";
                            $stmt = $connect->query($query);
                            
                            if($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<div class="interest-option">';
                                    echo '<input type="checkbox" id="interest_'.$row['id'].'" name="interests[]" value="'.$row['id'].'">';
                                    echo '<label for="interest_'.$row['id'].'">'.$row['name'].'</label>';
                                    echo '</div>';
                                }
                            } else {
                                echo '<p>No interests found in database.</p>';
                            }
                        } catch(PDOException $e) {
                            echo '<p>Error loading interests: ' . $e->getMessage() . '</p>';
                        }
                        ?>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Save Interests</button>
            </fieldset>
        </form>

        <!-- Info Section -->
        <h2>Why This Survey Matters</h2>
        <p class="info">By selecting your interests, we can better understand your personality and preferences, making it easier to find someone who aligns with your values and interests.</p>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
    </footer>

    <style>
        .interests-selection {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }
        
        .interest-option {
            background: #f0f0f0;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .interest-option:hover {
            background: #e0e0e0;
        }
        
        .interest-option input[type="checkbox"] {
            display: none;
        }
        
        .interest-option input[type="checkbox"]:checked + label {
            font-weight: bold;
            color: #e91e63;
        }
        
        .submit-btn {
            background: #e91e63;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            transition: background 0.3s;
        }
        
        .submit-btn:hover {
            background: #c2185b;
        }
    </style>
</body>
</html>