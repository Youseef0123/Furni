<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Include database connection
include 'index.php';

// Handle GET requests to fetch wishlist items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $session_id = isset($_GET['session_id']) ? $_GET['session_id'] : 'fixed_session_id'; // Use a fixed session_id if not provided

    // Prepare query to fetch wishlist items based on session_id
    $sql = "
        SELECT products.id, products.name, products.description, products.price, products.image_url 
        FROM wishlist_entries 
        INNER JOIN products ON wishlist_entries.product_id = products.id
        WHERE wishlist_entries.session_id = ?
    ";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "Error preparing statement: " . $conn->error]);
        exit;
    }

    // Bind parameters
    $stmt->bind_param("s", $session_id);

    // Execute query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $wishlist_items = [];

        while ($row = $result->fetch_assoc()) {
            $wishlist_items[] = $row;
        }

        // Return wishlist items as JSON
        echo json_encode($wishlist_items);
    } else {
        echo json_encode(["error" => "Execution error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
