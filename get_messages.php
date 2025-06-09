<?php
require_once "checklogin.php";

header('Content-Type: application/json');

$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : 0;
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

if (!$receiver_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid receiver ID']);
    exit;
}

try {
    // Get messages where current user is either sender or receiver
    $stmt = $connect->prepare("
        SELECT id, sender_id, receiver_id, message, sent_at 
        FROM chatmessage 
        WHERE (
            (sender_id = ? AND receiver_id = ?) 
            OR 
            (sender_id = ? AND receiver_id = ?)
        )
        AND id > ?
        ORDER BY sent_at ASC
    ");
    
    $stmt->execute([
        $_SESSION['user_id'], $receiver_id,
        $receiver_id, $_SESSION['user_id'],
        $last_id
    ]);
    
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
} catch(PDOException $e) {
    error_log("Error retrieving messages: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
} 