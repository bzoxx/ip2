<?php
require_once "checklogin.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $user_id = $_SESSION['user_id'];
        
        // Start transaction
        $connect->beginTransaction();
        
        // Delete all existing interests for this user
        $delete_query = "DELETE FROM user_interest WHERE user_id = :user_id";
        $delete_stmt = $connect->prepare($delete_query);
        $delete_stmt->execute(['user_id' => $user_id]);
        
        // Insert new interests
        if (isset($_POST['interests']) && is_array($_POST['interests'])) {
            $insert_query = "INSERT INTO user_interest (user_id, interest_id) VALUES (:user_id, :interest_id)";
            $insert_stmt = $connect->prepare($insert_query);
            
            foreach ($_POST['interests'] as $interest_id) {
                $insert_stmt->execute([
                    'user_id' => $user_id,
                    'interest_id' => $interest_id
                ]);
            }
        }
        
        // Commit transaction
        $connect->commit();
        
        // Redirect with success message
        header("Location: settings.php?success=interests_updated");
        exit();
        
    } catch (PDOException $e) {
        // Rollback on error
        $connect->rollBack();
        header("Location: settings.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // If not POST request, redirect to settings
    header("Location: settings.php");
    exit();
}
?> 