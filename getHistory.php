<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo 'Error: You are not logged in.';
    exit;
}

$username = $_SESSION['username'];
$host = "localhost";
$dbname = "gamezonedb";
$user = "root";
$password = "";  // Typically empty for local environments, not recommended for production

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM buying WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $output = "Purchase History:\n";
        foreach ($result as $row) {
            $output .= "ProductID: " . $row['productID'] . ", Quantity: " . $row['quantity'] . "\n";
        }
        echo $output;
    } else {
        echo "No purchase history found.";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
