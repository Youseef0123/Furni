<?php
include 'index.php';

// Add CORS headers to allow requests from all origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Fetch the incoming request data in JSON format
$data = json_decode(file_get_contents('php://input'), true);

// Check for the required data
if (!isset($data['cart_id']) || !isset($data['product_id']) || !isset($data['quantity'])) {
    echo json_encode(["status" => "error", "message" => "Required data is missing"]);
    exit;
}

$cart_id = $data['cart_id'];
$product_id = $data['product_id'];
$quantity = $data['quantity'];

// Secure the query to prevent SQL Injection issues
$cart_id = $conn->real_escape_string($cart_id);
$product_id = $conn->real_escape_string($product_id);
$quantity = $conn->real_escape_string($quantity);

// Check that the new quantity is greater than 0
if ($quantity < 1) {
    echo json_encode(["status" => "error", "message" => "Quantity must be greater than zero"]);
    exit;
}

$sql = "UPDATE Cart_Items SET quantity = '$quantity' WHERE cart_id = '$cart_id' AND product_id = '$product_id'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error: " . $sql . "<br>" . $conn->error]);
}

$conn->close();
?>
