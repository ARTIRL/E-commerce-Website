<?php

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
    echo "Unable to connect: " . $e->getMessage();
    exit;
}

// Check if form was submitted and that POST is not empty
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    // Retrieve user input
    $username = $_POST['userName'];
    $name = $_POST['firstName'];
    $email = $_POST['email'];
    $number = $_POST['mobile'];
    $message = $_POST['message'];

    // Prepare an SQL statement to avoid SQL injection
    $sqlq = "INSERT INTO feedbacks (username, message, email, number, name) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlq);
    
    // Bind parameters and execute the statement
    $stmt->bindParam(1, $username);
    $stmt->bindParam(2, $message);
    $stmt->bindParam(3, $email);
    $stmt->bindParam(4, $number);
    $stmt->bindParam(5, $name);

    if ($stmt->execute()) {
        echo "Message sent!";
        $alert = "Thank you for your feedback!";
        header("Location: contact.html?alert=" . urlencode($alert));
        exit();
    } else {
        echo "Failed to send message.";
    }
    
    $stmt = null; // Close the statement
}

$conn = null; // Close the connection
?>
