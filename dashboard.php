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
    $items_per_page = 3; // or however many users you want per page
$currentpage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($currentpage - 1) * $items_per_page;

    // Fetch users of opposite gender
    $query = "SELECT id, username, gender, preferences FROM users 
    WHERE id != :current_user_id AND gender = :opposite_gender 
    LIMIT :limit OFFSET :offset";

$stmt = $connect->prepare($query);
$stmt->bindParam(':current_user_id', $current_user_id);
$stmt->bindParam(':opposite_gender', $opposite_gender);
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch current user's interests
    $current_user_interests_query = "SELECT interest_id FROM user_interest WHERE user_id = :user_id";
    $interests_stmt = $connect->prepare($current_user_interests_query);
    $interests_stmt->execute(['user_id' => $current_user_id]);
    $current_user_interests = $interests_stmt->fetchAll(PDO::FETCH_COLUMN);
    $count_query = "SELECT COUNT(*) FROM users WHERE id != :current_user_id AND gender = :opposite_gender";
    $count_stmt = $connect->prepare($count_query);
    $count_stmt->execute([
        'current_user_id' => $current_user_id,
        'opposite_gender' => $opposite_gender
    ]);
    $total_users = $count_stmt->fetchColumn();
    $total_pages = ceil($total_users / $items_per_page);
    
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

        /* Filter Section Styles */
        .filter-section {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .interest-filter, .status-filter {
            margin-bottom: 20px;
        }

        .interest-filter h3, .status-filter h3 {
            margin-bottom: 15px;
            color: #333;
            font-size: 1.2em;
        }

        .interests-filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .interest-filter-tag {
            padding: 8px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 20px;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #333;
        }

        .interest-filter-tag:hover {
            background: #f5f5f5;
        }

        .interest-filter-tag.active {
            background: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }

        .status-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .status-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background: #f5f5f5;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #333;
        }

        .status-btn:hover {
            background: #e0e0e0;
        }

        .status-btn.active {
            background: #2196F3;
            color: white;
        }

        .status-btn i {
            font-size: 1.1em;
        }

        /* Simple Pagination Styles */
        .simple-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin: 30px 0;
            padding: 20px;
        }

        .page-numbers {
            display: flex;
            gap: 10px;
        }

        .page-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: white;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .page-number:hover {
            background: #f5f5f5;
            color: #2196F3;
        }

        .page-number.active {
    font-weight: bold;
    color: #fff;
    background-color: #007bff;
    border-radius: 4px;
    padding: 5px 10px;
}


        .page-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 20px;
            background: white;
            color: #333;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }

        .page-btn:hover {
            background: #f5f5f5;
            color: #2196F3;
        }

        .page-btn i {
            font-size: 0.9em;
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
        
        <!-- Filter Section -->
        <div class="filter-section">
            <!-- Interest Filter -->
            <div class="interest-filter">
                <h3>Filter by Interests</h3>
                <div class="interests-filter-tags">
                    <?php foreach ($all_interests as $id => $name): ?>
                        <button class="interest-filter-tag" data-interest-id="<?= $id ?>">
                            <?= htmlspecialchars($name) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="status-filter">
                <h3>Filter by Status</h3>
                <div class="status-buttons">
                    <button class="status-btn active" data-status="all">
                        <i class="fas fa-users"></i> All Users
                    </button>
                    <button class="status-btn" data-status="to-match">
                        <i class="fas fa-heart"></i> To Match
                    </button>
                    <button class="status-btn" data-status="pending">
                        <i class="fas fa-clock"></i> Pending
                    </button>
                    <button class="status-btn" data-status="to-accept">
                        <i class="fas fa-check"></i> To Accept
                    </button>
                    <button class="status-btn" data-status="chat">
                        <i class="fas fa-comments"></i> Chat
                    </button>
                </div>
            </div>
        </div>

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

        <?php
// Debug output - remove once confirmed working
// echo "<pre>";
// echo "Total pages: $total_pages\n";
// echo "Current page: $currentpage\n";
// echo "</pre>";
?>

<!-- Always show for testing (change back to $total_pages > 1 in production) -->
<?php if (true): ?>
    <div class="simple-pagination">
        <?php if ($currentpage > 1): ?>
            <a href="?page=<?= $currentpage - 1 ?>" class="page-btn">
                <i class="fas fa-angle-left"></i> Previous
            </a>
        <?php endif; ?>

        <div class="page-numbers">
            <?php
            if ($total_pages > 0):
                for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-number <?= ($i == $currentpage) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor;
            else:
                echo "<span>No pages</span>";
            endif;
            ?>
        </div>

        <?php if ($currentpage < $total_pages): ?>
            <a href="?page=<?= $currentpage + 1 ?>" class="page-btn">
                Next <i class="fas fa-angle-right"></i>
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>




    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Interest filter functionality
            const interestTags = document.querySelectorAll('.interest-filter-tag');
            const userCards = document.querySelectorAll('.user-card');

            interestTags.forEach(tag => {
                tag.addEventListener('click', function() {
                    this.classList.toggle('active');
                    const interestId = this.dataset.interestId;
                    
                    userCards.forEach(card => {
                        const interests = card.querySelectorAll('.interest-tag');
                        let hasInterest = false;
                        
                        interests.forEach(interest => {
                            if (interest.textContent.includes(this.textContent.trim())) {
                                hasInterest = true;
                            }
                        });

                        if (this.classList.contains('active')) {
                            if (!hasInterest) {
                                card.style.display = 'none';
                            }
                        } else {
                            // Check if any other active filters are hiding this card
                            let shouldShow = true;
                            document.querySelectorAll('.interest-filter-tag.active').forEach(activeTag => {
                                if (activeTag !== this) {
                                    let hasOtherInterest = false;
                                    interests.forEach(interest => {
                                        if (interest.textContent.includes(activeTag.textContent.trim())) {
                                            hasOtherInterest = true;
                                        }
                                    });
                                    if (!hasOtherInterest) {
                                        shouldShow = false;
                                    }
                                }
                            });
                            card.style.display = shouldShow ? '' : 'none';
                        }
                    });
                });
            });

            // Status filter functionality
            const statusButtons = document.querySelectorAll('.status-btn');
            
            statusButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    statusButtons.forEach(btn => btn.classList.remove('active'));
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const status = this.dataset.status;
                    
                    userCards.forEach(card => {
                        const actionButton = card.querySelector('.action-button');
                        const buttonText = actionButton.textContent.trim().toLowerCase();
                        
                        switch(status) {
                            case 'to-match':
                                card.style.display = buttonText.includes('match') ? '' : 'none';
                                break;
                            case 'pending':
                                card.style.display = buttonText.includes('pending') ? '' : 'none';
                                break;
                            case 'to-accept':
                                card.style.display = buttonText.includes('accept') ? '' : 'none';
                                break;
                            case 'chat':
                                card.style.display = buttonText.includes('chat') ? '' : 'none';
                                break;
                            default: // 'all'
                                card.style.display = '';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html> 
