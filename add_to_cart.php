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

// Check product availability and fetch the current quantity
$stmt = $conn->prepare("SELECT quantity FROM products WHERE productname = :itemName FOR UPDATE");
$stmt->bindParam(':itemName', $itemName);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product || $product['quantity'] < $quantity) {
    http_response_code(400); // Bad Request
    exit("Insufficient quantity available.");
}

// Begin transaction
$conn->beginTransaction();

try {
    // Insert purchase details into 'buying' table
    $insert_query = "INSERT INTO buying (username, productID, quantity, total_price) VALUES (:username, :itemName, :quantity, :totalPrice)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bindParam(':username', $_SESSION['username']);
    $stmt->bindParam(':itemName', $itemName);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':totalPrice', $totalPrice);
    $stmt->execute();

    // Update product quantity in the 'products' table
    $update_query = "UPDATE products SET quantity = quantity - :quantity WHERE productname = :itemName";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
    $stmt->bindParam(':itemName', $itemName);
    $stmt->execute();

    // Commit transaction
    $conn->commit();
    http_response_code(200); // OK
    echo "Item added to cart successfully and inventory updated.";
} catch (PDOException $e) {
    $conn->rollBack();
    http_response_code(500); // Internal Server Error
    echo "Failed to add item to cart: " . $e->getMessage();
}

// Close the statement and connection
$stmt = null;
$conn = null;
?>
// edited the table products to actually focus on quantity before getting to execute the transaction//
