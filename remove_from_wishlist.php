<?php
include 'index.php'; // Ensure the database connection is included here

// Set the content type to JSON
header('Content-Type: application/json');

// Add CORS headers to allow requests from any origin
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read the incoming request data in JSON format
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (isset($data['product_id'])) {
        $product_id = $data['product_id'];

        // Use Prepared Statements to prevent SQL Injection
        $stmt = $conn->prepare("DELETE FROM wishlist_entries WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Successfully removed from the wishlist'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $stmt->error
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Required data is missing'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Request is not of type POST'
    ]);
}

$conn->close();
?>
