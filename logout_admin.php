<?php
session_start();

// Clear admin session
unset($_SESSION['admin_logged_in']);

// Clear admin cookie
if (isset($_COOKIE['admin_auth'])) {
    // Remove the token from storage file
    $tokens_file = __DIR__ . '/admin_tokens.txt';
    if (file_exists($tokens_file)) {
        $tokens = file_get_contents($tokens_file);
        $tokens = str_replace($_COOKIE['admin_auth'], '', $tokens);
        $tokens = preg_replace('/\n+/', "\n", $tokens); // Clean up empty lines
        file_put_contents($tokens_file, trim($tokens));
    }
    
    // Delete the cookie by setting expiration in the past
    setcookie('admin_auth', '', time() - 3600, '/', '', true, true);
}

// Destroy the entire session
session_destroy();

// Redirect to admin login
header("Location: admindash.php");
exit; 
