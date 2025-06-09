<?php
require_once "checklogin.php";

$message = '';
$user_data = null;

try {
    $connect->exec("USE dating_app");
    
    // Fetch user data
    $query = "SELECT * FROM users WHERE id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle profile update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $preferences = trim($_POST['preferences'] ?? '');
        $birthdate = $_POST['birthdate'] ?? '';
        $gender = isset($_POST['gender']) ? ($_POST['gender'] === 'female' ? 0 : 1) : $user_data['gender'];

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format";
        } else {
            // Check if email exists for other users
            $email_check = $connect->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
            $email_check->bindParam(':email', $email);
            $email_check->bindParam(':user_id', $_SESSION['user_id']);
            $email_check->execute();

            if ($email_check->rowCount() > 0) {
                $message = "Email already exists";
            } else {
                // Update profile
                $update = $connect->prepare("UPDATE users SET username = :username, email = :email, 
                                          preferences = :preferences, birthdate = :birthdate, gender = :gender 
                                          WHERE id = :user_id");
                
                $update->bindParam(':username', $username);
                $update->bindParam(':email', $email);
                $update->bindParam(':preferences', $preferences);
                $update->bindParam(':birthdate', $birthdate);
                $update->bindParam(':gender', $gender);
                $update->bindParam(':user_id', $_SESSION['user_id']);

                if ($update->execute()) {
                    $message = "Profile updated successfully!";
                    // Refresh user data
                    $stmt->execute();
                    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $message = "Error updating profile";
                }
            }
        }
    }
} catch(PDOException $e) {
    error_log("Error in profile.php: " . $e->getMessage());
    $message = "An error occurred. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Blind Date Hub</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="js/common.js" defer></script>
</head>
<body>
    <?php require_once "includes/header.php"; ?>

    <main class="profile-main">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                    <div class="profile-status">
                        <?php if ($user_data['gender'] == 1): ?>
                            <span class="gender-icon male"><i class="fas fa-mars"></i></span>
                        <?php else: ?>
                            <span class="gender-icon female"><i class="fas fa-venus"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
                <h1><?= htmlspecialchars($user_data['username']) ?></h1>
            </div>

            <?php if ($message): ?>
                <div class="alert <?= strpos($message, 'success') !== false ? 'alert-success' : 'alert-error' ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" 
                           value="<?= htmlspecialchars($user_data['username']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="birthdate">
                        <i class="fas fa-calendar"></i> Birth Date
                    </label>
                    <input type="date" id="birthdate" name="birthdate" 
                           value="<?= htmlspecialchars($user_data['birthdate']) ?>" required>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-venus-mars"></i> Gender
                    </label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="gender" value="female" 
                                   <?= $user_data['gender'] == 0 ? 'checked' : '' ?>> Female
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="gender" value="male" 
                                   <?= $user_data['gender'] == 1 ? 'checked' : '' ?>> Male
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="preferences">
                        <i class="fas fa-heart"></i> About Me & Preferences
                    </label>
                    <textarea id="preferences" name="preferences" rows="4"><?= htmlspecialchars($user_data['preferences']) ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn update-btn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
