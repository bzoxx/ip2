<?php
require_once "checklogin.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$receiver_id || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit;
}

try {
    $stmt = $connect->prepare("INSERT INTO chatmessage (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $receiver_id, $message]);
    
    echo json_encode([
        'success' => true,
        'message_id' => $connect->lastInsertId()
    ]);
} catch(PDOException $e) {
    error_log("Error sending message: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error']);
} 