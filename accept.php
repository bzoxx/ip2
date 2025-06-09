<?php
require_once "checklogin.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$match_id = $_GET['id'];
$current_user = $_SESSION['user_id'];

try {
    $connect->exec("USE dating_app");
    
    // Verify this match belongs to the current user
    $check_query = "SELECT * FROM matches WHERE id = :match_id AND wtih = :current_user AND approved = 0";
    $check_stmt = $connect->prepare($check_query);
    $check_stmt->bindParam(':match_id', $match_id);
    $check_stmt->bindParam(':current_user', $current_user);
    $check_stmt->execute();
    
    if ($check_stmt->rowCount() == 0) {
        // Match not found or not valid for acceptance
        header("Location: dashboard.php");
        exit;
    }
    
    // Update match to approved
    $update_query = "UPDATE matches SET approved = 1 WHERE id = :match_id";
    $update_stmt = $connect->prepare($update_query);
    $update_stmt->bindParam(':match_id', $match_id);
    
    if ($update_stmt->execute()) {
        header("Location: dashboard.php");
    } else {
        echo "Error accepting match. Please try again.";
    }
    
} catch(PDOException $e) {
    error_log("Error in accept.php: " . $e->getMessage());
    echo "An error occurred. Please try again later.";
}
?> 
