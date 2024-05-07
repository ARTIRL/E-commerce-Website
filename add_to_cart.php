<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    exit('User not logged in'); // More informative exit message
}

// Database connection parameters
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "gamezonedb";

try {
    // Establish a new PDO connection
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get item details from POST request
$itemName = isset($_POST['name']) ? $_POST['name'] : '';
$itemPrice = isset($_POST['price']) ? $_POST['price'] : '';
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : '';
$totalPrice = $itemPrice * $quantity;

// Prepare an SQL statement to avoid SQL injection
$insert_query = "INSERT INTO buying (username, productID, quantity, total_price) VALUES (:username, :itemName, :quantity, :totalPrice)";
$stmt = $conn->prepare($insert_query);

// Bind parameters
$stmt->bindParam(':username', $_SESSION['username']);
$stmt->bindParam(':itemName', $itemName);
$stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
$stmt->bindParam(':totalPrice', $totalPrice);

// Execute the statement
try {
    $stmt->execute();
    // Item added to cart successfully
    http_response_code(200); // Set HTTP response status code to 200 (OK)
    echo "Item added to cart successfully.";
} catch (PDOException $e) {
    // Failed to add item to cart
    http_response_code(500); // Set HTTP response status code to 500 (Internal Server Error)
    echo "Failed to add item to cart: " . $e->getMessage();
}

// Close the statement and connection
$stmt = null;
$conn = null;
?>
