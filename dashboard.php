<?php 
require_once "checklogin.php";

// Get current user's ID and gender
$current_user_id = $_SESSION['user_id'];

try {
    $connect->exec("USE dating_app");
    
    // First get current user's gender
    $gender_query = "SELECT gender FROM users WHERE id = :user_id";
    $gender_stmt = $connect->prepare($gender_query);
    $gender_stmt->execute(['user_id' => $current_user_id]);
    $current_user_gender = $gender_stmt->fetchColumn();
    
    // Calculate opposite gender (if current is 1, opposite is 0, and vice versa)
    $opposite_gender = $current_user_gender == 1 ? 0 : 1;
    
    // Fetch users of opposite gender
    $query = "SELECT id, username, gender, preferences FROM users WHERE id != :current_user_id AND gender = :opposite_gender";
    $stmt = $connect->prepare($query);
    $stmt->bindParam(':current_user_id', $current_user_id);
    $stmt->bindParam(':opposite_gender', $opposite_gender);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch current user's interests
    $current_user_interests_query = "SELECT interest_id FROM user_interest WHERE user_id = :user_id";
    $interests_stmt = $connect->prepare($current_user_interests_query);
    $interests_stmt->execute(['user_id' => $current_user_id]);
    $current_user_interests = $interests_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch all interests for reference
    $all_interests_query = "SELECT id, name FROM interests";
    $all_interests_stmt = $connect->query($all_interests_query);
    $all_interests = $all_interests_stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Fetch matches involving current user
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

    // Fetch interests for all users
    $user_interests = [];
    foreach ($users as $user) {
        $user_interests_query = "SELECT interest_id FROM user_interest WHERE user_id = :user_id";
        $user_interests_stmt = $connect->prepare($user_interests_query);
        $user_interests_stmt->execute(['user_id' => $user['id']]);
        $user_interests[$user['id']] = $user_interests_stmt->fetchAll(PDO::FETCH_COLUMN);
    }

} catch(PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = [];
    $match_lookup = [];
    $user_interests = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Matches - Blind Date Hub</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/dash.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="js/common.js" defer></script>
    <style>
        .interests-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin: 10px 0;
        }

        .interest-tag {
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8em;
            white-space: nowrap;
        }

        .shared-interest {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #a5d6a7;
        }

        .other-interest {
            background-color: #f5f5f5;
            color: #757575;
            border: 1px solid #e0e0e0;
        }

        .user-card {
            position: relative !important;
            overflow: visible !important;
        }

        .match-percentage {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .user-card:hover .match-percentage {
            transform: translateY(-2px);
        }

        /* Match percentage variants */
        .match-percentage i {
            font-size: 1em;
        }

        /* Different styling based on match text */
        .match-percentage:has(i.fa-stars) {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: #000;
        }

        .match-percentage:has(i.fa-heart) {
            background: linear-gradient(45deg, #FF69B4, #FF1493);
            color: white;
        }

        .match-percentage:has(i.fa-thumbs-up) {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
        }

        .match-percentage:has(i.fa-question) {
            background: linear-gradient(45deg, #2196F3, #1976D2);
            color: white;
        }

        .match-percentage:has(i.fa-globe) {
            background: linear-gradient(45deg, #9E9E9E, #757575);
            color: white;
        }
    </style>
</head>
<body>
    <?php require_once "includes/header.php"; ?>

    <main><br>
<br>
<br>
<br>

        <h1>Find Your Match</h1>
        
        <div class="users-grid">
            <?php foreach ($users as $user): 
                // Calculate shared interests
                $user_interest_ids = $user_interests[$user['id']] ?? [];
                $shared_interests = array_intersect($current_user_interests, $user_interest_ids);
                $match_percentage = 0;
                if (!empty($current_user_interests) && !empty($user_interest_ids)) {
                    $match_percentage = round((count($shared_interests) / count($current_user_interests)) * 100);
                }

                // Determine match text based on percentage
                $match_text = '';
                $match_icon = '';
                if ($match_percentage >= 80) {
                    $match_text = 'Soulmate';
                    $match_icon = 'fa-stars';
                } elseif ($match_percentage >= 60) {
                    $match_text = 'Perfect Match';
                    $match_icon = 'fa-heart';
                } elseif ($match_percentage >= 40) {
                    $match_text = 'More Likely';
                    $match_icon = 'fa-thumbs-up';
                } elseif ($match_percentage >= 20) {
                    $match_text = 'Less Likely';
                    $match_icon = 'fa-question';
                } else {
                    $match_text = 'Different Worlds';
                    $match_icon = 'fa-globe';
                }
            ?>
                <div class="user-card">
                    <div class="match-percentage">
                        <i class="fas <?= $match_icon ?>"></i> <?= $match_text ?>
                    </div>
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
                        
                        <div class="interests-tags">
                            <?php 
                            foreach ($user_interest_ids as $interest_id):
                                $is_shared = in_array($interest_id, $shared_interests);
                                $class = $is_shared ? 'shared-interest' : 'other-interest';
                                $icon = $is_shared ? '<i class="fas fa-star"></i> ' : '';
                            ?>
                                <span class="interest-tag <?= $class ?>">
                                    <?= $icon . htmlspecialchars($all_interests[$interest_id]) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="action-button">
                        <?php
                        if (isset($match_lookup[$user['id']])) {
                            $match = $match_lookup[$user['id']];
                            if ($match['approved'] == 1) {
                                echo '<a href="chat.php?user=' . $user['id'] . '" class="btn chat">
                                        <i class="fas fa-comments"></i> Chat
                                    </a>';
                            } elseif ($match['status'] == 'initiated') {
                                echo '<button class="btn pending" disabled>
                                        <i class="fas fa-clock"></i> Pending
                                    </button>';
                            } else {
                                echo '<a href="accept.php?id=' . $match['match_id'] . '" class="btn accept">
                                        <i class="fas fa-check"></i> Accept
                                    </a>';
                            }
                        } else {
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
</body>
</html> 