<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // Close the response for OPTIONS requests
}

// Include database connection settings
include 'index.php';

// Handle POST requests to add an item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    $cart_id = isset($data['cart_id']) ? $data['cart_id'] : 1; // Ensure cart_id is passed in the request
    $product_id = isset($data['product_id']) ? $data['product_id'] : null;
    $quantity = isset($data['quantity']) ? $data['quantity'] : 1; // Default value for quantity

    if ($product_id === null) {
        echo json_encode(["error" => "Error: product_id is required"]);
        exit;
    }

    // Prepare the query
    $sql = "INSERT INTO Cart_Items (cart_id, product_id, quantity) VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "Error preparing the query: " . $conn->error]);
        exit;
    }

    // Bind the parameters
    $stmt->bind_param("iii", $cart_id, $product_id, $quantity);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["success" => "Item added successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

// Handle GET requests to retrieve the total number of items in the cart
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Read cart_id from the GET query
    $cart_id = isset($_GET['cart_id']) ? intval($_GET['cart_id']) : 1; // Default value for cart_id

    // Prepare the query
    $sql = "SELECT SUM(quantity) AS total_items FROM Cart_Items WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "Error preparing the query: " . $conn->error]);
        exit;
    }

    // Bind the parameters
    $stmt->bind_param("i", $cart_id);

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $total_items = $row['total_items'] ? $row['total_items'] : 0;
        echo json_encode(["total_items" => $total_items]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
