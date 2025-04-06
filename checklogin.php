<?php
session_start();
require_once "conn.php"; // This uses your conn.php with PDO setup

try {
    // Connect to the correct database
    $connect->exec("USE dating_app");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page or show error
        header("Location: login.php");
        exit;
    }

    // Optional: fetch user data if needed
    $stmt = $connect->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Invalid session user
        session_destroy();
        header("Location: login.php");
        exit;
    }

    // You can now use $user['name'], $user['email'], etc. safely on your page
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
