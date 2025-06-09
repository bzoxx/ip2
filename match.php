<?php
require_once "checklogin.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_to_match = $_GET['id'];
$current_user = $_SESSION['user_id'];

try {
    $connect->exec("USE dating_app");
    
    // Check if match already exists
    $check_query = "SELECT * FROM matches WHERE 
                   (connect = :user1 AND wtih = :user2) OR 
                   (connect = :user2 AND wtih = :user1)";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bindParam(':user1', $current_user);
    $check_stmt->bindParam(':user2', $user_to_match);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() > 0) {
        // Match already exists
        header("Location: dashboard.php");
        exit;
    }
    
    // Create new match
    $insert_query = "INSERT INTO matches (connect, wtih, approved) VALUES (:user1, :user2, 0)";
    $insert_stmt = $connect->prepare($insert_query);
    $insert_stmt->bindParam(':user1', $current_user);
    $insert_stmt->bindParam(':user2', $user_to_match);
    
    if ($insert_stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error creating match. Please try again.";
    }
    
} catch(PDOException $e) {
    error_log("Error in match.php: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
}
?> 