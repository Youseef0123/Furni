<?php
header("Access-Control-Allow-Origin: *"); // Allow all origins, or specify the exact origin
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include 'index.php'; // Database connection file

$cart_id = 1; // Default value for cart_id, you can modify it as needed

// Prepare the query to fetch all items from the cart
$sql = "SELECT p.product_id, p.name, p.price, p.image_url, ci.quantity 
        FROM Cart_Items ci 
        JOIN Products p ON ci.product_id = p.product_id 
        WHERE ci.cart_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error in preparing the query: " . $conn->error]);
    exit;
}

// Bind parameters
$stmt->bind_param("i", $cart_id);

// Execute the query
if (!$stmt->execute()) {
    echo json_encode(["error" => "Error in executing the query: " . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

// Collect data into an array
$cartItems = [];
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
}

// Check if the cart is empty
if (empty($cartItems)) {
    echo json_encode(["message" => "The cart is empty"]);
} else {
    // Send data as JSON
    echo json_encode($cartItems);
}

$stmt->close();
$conn->close();
?>
