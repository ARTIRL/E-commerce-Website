<?php
session_start();  // Ensure sessions are started, useful if you want to retain user info

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
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to avoid SQL injection
    $insert_query = "INSERT INTO users (username, irl_name, pd, email, Phone) VALUES (:username, :name, :hashed_password, :email, :phone)";

    // Prepare statement
    $stmt = $conn->prepare($insert_query);

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':hashed_password', $hashed_password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);

    // Execute the statement and check for success/failure
    try {
        $stmt->execute();
        $alert = "Thanks For Joining Us! \n Feel free to log in!";
        header("Location: INDEX.html?alert=" . urlencode($alert));
        exit();
    } catch (PDOException $e) {
        // If there's an SQL error, it likely means duplication or database issue
        $alert = "Seems the account is already in use: " . $e->getMessage();
        header("Location: INDEX.html?alert=" . urlencode($alert));
        exit();
    }
}
?>
