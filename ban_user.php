<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if user_id is provided
if (!isset($_POST['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit();
}

require_once 'conn.php';

try {
    // Update the connection to use the dating_app database
    $connect->exec("USE dating_app");
    
    // First, check if the user exists
    $stmt = $connect->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$_POST['user_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    // Add a 'banned' column if it doesn't exist
    $connect->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS banned TINYINT(1) DEFAULT 0");
    
    // Update the user's banned status
    $stmt = $connect->prepare("UPDATE users SET banned = 1 WHERE id = ?");
    $stmt->execute([$_POST['user_id']]);
    
    echo json_encode(['success' => true, 'message' => 'User banned successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?> 
