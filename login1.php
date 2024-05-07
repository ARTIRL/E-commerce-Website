<?php
session_start();

// Database connection parameters
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "gamezonedb";

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if username and password are provided
if (!isset($_POST['user'], $_POST['pd'])) {
    $_SESSION['error'] = 'Username and password are required.';
    header("Location: index.html?alert=" . urlencode($_SESSION['error']));
    exit();
}

$username = $_POST['user'];
$password = $_POST['pd'];

// Prepare and execute the SQL statement
$sql = "SELECT * FROM users WHERE username = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':username', $username);
$stmt->execute();

if ($stmt->rowCount() == 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hashed_password = $row['pd'];

    // Verify password
    if (password_verify($password, $hashed_password)) {
        // Password is correct, create session
        $_SESSION['username'] = $username;
        $alert = "Welcome, $username";
        // Redirect user to dashboard or another page
        header("Location: Welcome.html?alert=" . urlencode($alert));
        exit();
    } else {
        // Password is incorrect, set error message
        $alert = "Incorrect password.";
        header("Location: index.html?alert=" . urlencode($alert));
        exit();
    }
} else {
    // Username doesn't exist, set error message
    $alert = "User not found.";
    header("Location: index.html?alert=" . urlencode($alert));
    exit();
}

// Close the connection
$conn = null;
?>
