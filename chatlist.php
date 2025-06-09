<?php 
require_once "checklogin.php"; 

// Get users who have chatted with the current user
try {
    $stmt = $connect->prepare("
        SELECT 
            u.id,
            u.username,
            u.gender,
            latest_messages.message as last_message,
            latest_messages.sent_at as last_message_time,
            latest_messages.message_type
        FROM users u
        INNER JOIN (
            SELECT 
                CASE 
                    WHEN sender_id = ? THEN receiver_id
                    ELSE sender_id
                END as chat_user_id,
                message,
                sent_at,
                CASE 
                    WHEN sender_id = ? THEN 'sent'
                    ELSE 'received'
                END as message_type
            FROM chatmessage cm1
            WHERE (sender_id = ? OR receiver_id = ?)
            AND sent_at = (
                SELECT MAX(sent_at)
                FROM chatmessage cm2
                WHERE (
                    (cm2.sender_id = cm1.sender_id AND cm2.receiver_id = cm1.receiver_id)
                    OR 
                    (cm2.sender_id = cm1.receiver_id AND cm2.receiver_id = cm1.sender_id)
                )
            )
        ) latest_messages ON u.id = latest_messages.chat_user_id
        ORDER BY latest_messages.sent_at DESC
    ");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id'],
        $_SESSION['user_id']
    ]);
    
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching chats: " . $e->getMessage());
    $chats = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat List - Blind Date Hub</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/chatlist.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/png" href="images/logo.png">
    <script src="js/common.js" defer></script>
</head>
<body>
    <?php require_once "includes/header.php"; ?>

    <!-- Main Content -->
    <main>
        <h1>Your Messages</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'no_match'): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                You can only chat with users you have matched with.
            </div>
        <?php endif; ?>

        <section class="chat-list">
            <?php if (empty($chats)): ?>
                <div class="no-chats">
                    <p>You haven't started any conversations yet.</p>
                    <a href="dashboard.php" class="btn">Find Matches</a>
                </div>
            <?php else: ?>
                <?php foreach ($chats as $chat): ?>
                    <div class="chat-item" onclick="location.href='chat.php?user=<?= $chat['id'] ?>'">
                        <div class="avatar">
                            <img src="images/def.webp" alt="User Avatar">
                            <?php if ($chat['gender'] == 1): ?>
                                <span class="gender-icon male"><i class="fas fa-mars"></i></span>
                            <?php else: ?>
                                <span class="gender-icon female"><i class="fas fa-venus"></i></span>
                            <?php endif; ?>
                        </div>
                        <div class="chat-details">
                            <h3><?= htmlspecialchars($chat['username']) ?></h3>
                            <p><?= $chat['message_type'] === 'sent' ? 'You: ' : '' ?><?= htmlspecialchars(substr($chat['last_message'], 0, 50)) . (strlen($chat['last_message']) > 50 ? '...' : '') ?></p>
                        </div>
                        <div class="chat-time">
                            <span><?= date('g:i A', strtotime($chat['last_message_time'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>
