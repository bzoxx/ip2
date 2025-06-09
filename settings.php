<?php 
require_once "checklogin.php"; 

// Fetch user's current interests
try {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT interest_id FROM user_interest WHERE user_id = :user_id";
    $stmt = $connect->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $user_interests = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $error = "Error fetching interests: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Blind Cupid</title>
    <link rel="stylesheet" href="css/setting.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
</head>
<body>
    <!-- Header -->
    <?php require_once "includes/header.php"; ?>

    <!-- Main Content -->
    <main>
        <h1>Your Settings</h1>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'interests_updated'): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Your interests have been updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>



        <!-- Interests Settings -->
        <section class="settings-section">
            <h2><i class="fas fa-heart"></i> Your Interests</h2>
            <form action="update_interests.php" method="post">
                <div class="interests-grid">
                    <?php
                    try {
                        $query = "SELECT * FROM interests ORDER BY name";
                        $stmt = $connect->query($query);
                        
                        while($interest = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $checked = in_array($interest['id'], $user_interests) ? 'checked' : '';
                            echo '<div class="interest-option">';
                            echo '<input type="checkbox" id="interest_'.$interest['id'].'" 
                                         name="interests[]" value="'.$interest['id'].'" '.$checked.'>';
                            echo '<label for="interest_'.$interest['id'].'">'.$interest['name'].'</label>';
                            echo '</div>';
                        }
                    } catch(PDOException $e) {
                        echo '<p class="error">Error loading interests: ' . $e->getMessage() . '</p>';
                    }
                    ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Interests</button>
            </form>
        </section>

        <!-- Logout Section -->
        <section class="settings-section logout-section">
            <h2><i class="fas fa-sign-out-alt"></i> Logout</h2>
            <p>Click below to safely log out of your account</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </section>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Blind Cupid. All rights reserved.</p>
        <p><a href="about.php">About</a> | <a href="privacyPolicy.php">Privacy Policy</a> | <a href="terms.php">Terms of Service</a></p>
    </footer>

    <style>
        .settings-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .settings-section h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .interests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .interest-option {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .interest-option:hover {
            background: #e0e0e0;
        }

        .interest-option input[type="checkbox"] {
            margin-right: 8px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            text-decoration: none;
            display: inline-block;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .logout-section {
            text-align: center;
        }

        .logout-section p {
            margin-bottom: 15px;
            color: #666;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</body>
</html>
