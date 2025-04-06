<?php

$db_pass = "";
$db_host = "localhost";
$db_username = "root";

try {
    $connect = new PDO("mysql:host=$db_host;", $db_username, $db_pass);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database
    $connect->exec("CREATE DATABASE IF NOT EXISTS dating_app");
    echo "Database created successfully.<br>";

    // Use the database
    $connect->exec("USE dating_app");

    // Create users table (MATCHES signup form)
    $connect->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            preferences TEXT,
            birthdate DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'users' created.<br>";

    // Create interests table
    $connect->exec("
        CREATE TABLE IF NOT EXISTS interests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL
        )
    ");
    echo "Table 'interests' created.<br>";

    // Insert dummy interests
    $connect->exec("
        INSERT IGNORE INTO interests (name) VALUES 
        ('Music'), 
        ('Sports'), 
        ('Travel'), 
        ('Movies'), 
        ('Reading'),
        ('Cooking'),
        ('Fitness'),
        ('Gaming')
    ");
    echo "Dummy data inserted into 'interests'.<br>";

    // Create user_interest table (many-to-many)
    $connect->exec("
        CREATE TABLE IF NOT EXISTS user_interest (
            user_id INT NOT NULL,
            interest_id INT NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (interest_id) REFERENCES interests(id) ON DELETE CASCADE,
            PRIMARY KEY (user_id, interest_id)
        )
    ");
    echo "Table 'user_interest' created.<br>";

    // Create chatmessage table
    $connect->exec("
        CREATE TABLE IF NOT EXISTS chatmessage (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            message TEXT NOT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "Table 'chatmessage' created.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
