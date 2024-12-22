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

// Handle POST requests to add an item to the wishlist
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);
    
    $session_id = isset($data['session_id']) ? $data['session_id'] : 'fixed_session_id'; // Ensure session_id is passed in the request
    $product_id = isset($data['product_id']) ? $data['product_id'] : null;

    if ($product_id === null) {
        echo json_encode(["error" => "Error: product_id is required"]);
        exit;
    }

    // Prepare the query to check if the item exists
    $checkSql = "SELECT * FROM wishlist_entries WHERE session_id = ? AND product_id = ?";
    $stmt = $conn->prepare($checkSql);

    if ($stmt === false) {
        echo json_encode(["error" => "Error preparing the query: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("si", $session_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // The item does not exist, add the item
        $sql = "INSERT INTO wishlist_entries (session_id, product_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["error" => "Error preparing the query: " . $conn->error]);
            exit;
        }

        $stmt->bind_param("si", $session_id, $product_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => "Item added successfully"]);
        } else {
            echo json_encode(["error" => "Error: " . $stmt->error]);
        }
    } else {
        // The item already exists
        echo json_encode(["error" => "Item already exists"]);
    }

    $stmt->close();
}

$conn->close();
?>
