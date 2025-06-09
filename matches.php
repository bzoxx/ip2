<?php 
require_once "checklogin.php";

// Get current user's ID
$current_user_id = $_SESSION['user_id'];

try {
    $connect->exec("USE dating_app");
    
    // Fetch all users except current user
    $query = "SELECT id, username, gender, preferences FROM users WHERE id != :current_user_id";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':current_user_id', $current_user_id);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all matches involving current user
    $matches_query = "SELECT * FROM matches WHERE connect = :user_id OR wtih = :user_id";
    $matches_stmt = $connect->prepare($matches_query);
    $matches_stmt->bindParam(':user_id', $current_user_id);
    $matches_stmt->execute();
    $matches = $matches_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a lookup array for matches
    $match_lookup = [];
    foreach ($matches as $match) {
        if ($match['connect'] == $current_user_id) {
            $match_lookup[$match['wtih']] = [
                'status' => 'initiated',
                'approved' => $match['approved'],
                'match_id' => $match['id']
            ];
        } else {
            $match_lookup[$match['connect']] = [
                'status' => 'received',
                'approved' => $match['approved'],
                'match_id' => $match['id']
            ];
        }
    }
} catch(PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = [];
    $match_lookup = [];
}

require_once "includes/header.php";
?>

<main>
    <h1>Find Your Match</h1>
    
    <div class="users-grid">
        <?php foreach ($users as $user): ?>
            <div class="user-card">
                <div class="user-image">
                    <img src="images/def.webp" alt="Profile picture of <?= htmlspecialchars($user['username']) ?>">
                    <?php if ($user['gender'] == 1): ?>
                        <span class="gender-icon male"><i class="fas fa-mars"></i></span>
                    <?php else: ?>
                        <span class="gender-icon female"><i class="fas fa-venus"></i></span>
                    <?php endif; ?>
                </div>
                
                <div class="user-info">
                    <h3><?= htmlspecialchars($user['username']) ?></h3>
                    <p class="preferences"><?= htmlspecialchars(substr($user['preferences'], 0, 100)) . (strlen($user['preferences']) > 100 ? '...' : '') ?></p>
                </div>

                <div class="action-button">
                    <?php
                    if (isset($match_lookup[$user['id']])) {
                        $match = $match_lookup[$user['id']];
                        if ($match['approved'] == 1) {
                            // Match approved - show chat button
                            echo '<a href="chat.php?user=' . $user['id'] . '" class="btn chat">
                                    <i class="fas fa-comments"></i> Chat
                                </a>';
                        } elseif ($match['status'] == 'initiated') {
                            // Current user initiated - show pending
                            echo '<button class="btn pending" disabled>
                                    <i class="fas fa-clock"></i> Pending
                                </button>';
                        } else {
                            // Current user received - show accept button
                            echo '<a href="accept.php?id=' . $match['match_id'] . '" class="btn accept">
                                    <i class="fas fa-check"></i> Accept
                                </a>';
                        }
                    } else {
                        // No match exists - show match button
                        echo '<a href="match.php?id=' . $user['id'] . '" class="btn match">
                                <i class="fas fa-heart"></i> Match
                            </a>';
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
