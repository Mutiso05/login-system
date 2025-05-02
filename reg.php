<?php
// SESSION OPTIONAL FOR REGISTRATION
// session_start(); 

// Auto-create DB and table
$host = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "ronohdan";

// Connect to MySQL
$conn = new mysqli($host, $db_username, $db_password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Create table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS dann (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Only run registration if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        // Could redirect to error.html or show message
        die("Username and password are required.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and bind insert statement
    $stmt = $conn->prepare("INSERT INTO dann (username, password) VALUES (?, ?)");

    if ($stmt) {
        $stmt->bind_param("ss", $username, $hashed_password);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            // ✅ THIS IS THE REDIRECT YOU WANT
            header("Location: login.html");
            exit();
        } else {
            // Registration failed (maybe duplicate username)
         die("Error: " . $stmt->error);
        }
    } else {
        die("Prepare failed: " . $conn->error);
    }
}
?>
