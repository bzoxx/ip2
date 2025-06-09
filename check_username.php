<?php
require_once "conn.php";

if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    
    try {
        $connect->exec("USE dating_app");
        $stmt = $connect->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $count = $stmt->fetchColumn();
        
        echo json_encode([
            'available' => $count === 0,
            'message' => $count === 0 ? 'Username is available' : 'Username is already taken'
        ]);
    } catch(PDOException $e) {
        echo json_encode([
            'error' => true,
            'message' => 'Error checking username'
        ]);
    }
}
?> 
