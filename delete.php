<?php
session_start();

$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "gamezonedb";

try {
    // Create PDO connection
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Unable to connect: " . $e->getMessage();
    exit();
}

// Proceed only if there is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];

    // First, verify user's credentials
    $stmt = $conn->prepare("SELECT pd FROM users WHERE username = :username AND email = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['pd'])) {
        // Begin transaction
        $conn->beginTransaction();
        
        try {
            // Delete related entries from 'feedbacks' table
            $stmt = $conn->prepare("DELETE FROM feedbacks WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            // Delete related entries from 'buying' table
            $stmt = $conn->prepare("DELETE FROM buying WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            // Delete user from 'users' table
            $stmt = $conn->prepare("DELETE FROM users WHERE username = :username AND email = :email");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Commit transaction
            $conn->commit();
            $alert = "Account and related data have been successfully deleted.";
            header("Location: Welcome.html?alert=" . urlencode($alert));
            exit();
        } catch (PDOException $e) {
            // Rollback transaction in case of error
            $conn->rollBack();
            echo "Error: " . $e->getMessage();
            exit();
        }
    } else {
        // Password is incorrect or user does not exist
        $alert = "Invalid credentials provided.";
        header("Location: index.html?alert=" . urlencode($alert));
        exit();
    }
}
?>
