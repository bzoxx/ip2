<?php
require_once "checklogin.php";

try {
    // Begin transaction
    $connect->beginTransaction();

    // Delete existing user interests
    $delete_query = "DELETE FROM user_interest WHERE user_id = :user_id";
    $stmt = $connect->prepare($delete_query);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    // Insert new user interests if any were selected
    if (!empty($_POST['interests']) && is_array($_POST['interests'])) {
        $insert_query = "INSERT INTO user_interest (user_id, interest_id) VALUES (:user_id, :interest_id)";
        $stmt = $connect->prepare($insert_query);
        
        foreach ($_POST['interests'] as $interest_id) {
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->bindParam(':interest_id', $interest_id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    // Commit transaction
    $connect->commit();

    // Redirect back to dashboard
    header("Location: dashboard.php");
    exit();

} catch(PDOException $e) {
    // Rollback transaction if error occurs
    $connect->rollBack();
    
    // Handle error (you might want to log this and show a user-friendly message)
    error_log("Error saving interests: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while saving your interests. Please try again.";
    header("Location: interest.php");
    exit();
}
?>
