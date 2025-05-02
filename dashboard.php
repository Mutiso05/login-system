<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "ronohdan";

// Connect to DB
$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Create submissions table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO submissions (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();
        echo "<p style='color: green;'>Submission successful!</p>";
    } else {
        echo "<p style='color: red;'>Message cannot be empty.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
</head>
<body>
    <h2>Welcome to Your Dashboard</h2>
    <form method="POST" action="">
        <textarea name="message" rows="4" cols="50" placeholder="Enter your message..." required></textarea><br><br>
        <button type="submit">Submit Message</button>
    </form>
    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
