<?php
session_start();

// Admin credentials (in a real application, this should be in a secure configuration file)
define('ADMIN_PASSWORD', 'admin123'); // Change this to a strong password
define('COOKIE_NAME', 'admin_auth');
define('COOKIE_DURATION', 60 * 60 * 24 * 7); // 7 days

// Function to generate a secure token
function generateSecureToken() {
    return bin2hex(random_bytes(32));
}

// Check if admin is already logged in via session or cookie
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Check for cookie authentication
    if (isset($_COOKIE[COOKIE_NAME])) {
        $stored_token = $_COOKIE[COOKIE_NAME];
        // In a real application, you would verify this token against a database
        // For this example, we'll use a simple file-based storage
        $tokens_file = __DIR__ . '/admin_tokens.txt';
        if (file_exists($tokens_file)) {
            $valid_tokens = file_get_contents($tokens_file);
            if (strpos($valid_tokens, $stored_token) !== false) {
                $_SESSION['admin_logged_in'] = true;
                // Refresh the cookie
                setcookie(COOKIE_NAME, $stored_token, time() + COOKIE_DURATION, '/', '', true, true);
            }
        }
    }
}

// If still not logged in, check for login form submission
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_password'])) {
        if ($_POST['admin_password'] === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
            
            // Generate and store authentication token
            $token = generateSecureToken();
            
            // Store token in cookie
            setcookie(COOKIE_NAME, $token, time() + COOKIE_DURATION, '/', '', true, true);
            
            // Store token in file (in a real application, use a database)
            $tokens_file = __DIR__ . '/admin_tokens.txt';
            file_put_contents($tokens_file, $token . PHP_EOL, FILE_APPEND);
            
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = "Invalid password";
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - Blind Date Hub</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .login-container {
                background: white;
                padding: 2rem;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                width: 100%;
                max-width: 400px;
            }
            h1 {
                text-align: center;
                color: #333;
                margin-bottom: 1.5rem;
            }
            .error {
                color: #dc3545;
                text-align: center;
                margin-bottom: 1rem;
            }
            form {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            input[type="password"] {
                padding: 0.75rem;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 1rem;
            }
            button {
                padding: 0.75rem;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 1rem;
                cursor: pointer;
            }
            button:hover {
                background-color: #0056b3;
            }
            .remember-me {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                margin-top: 0.5rem;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1>Admin Login</h1>
            <?php if (isset($error_message)): ?>
                <div class="error"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="admin_password" placeholder="Enter admin password" required>
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember" checked>
                    <label for="remember">Remember me for 7 days</label>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?> 
