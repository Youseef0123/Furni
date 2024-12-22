<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

include 'index.php'; //connection with database through the index

$session_id = 'fixed_session_id'; // fixed session 

// get the data by post api 
$data = json_decode(file_get_contents("php://input"));

if (isset($data->product_id)) {
    $product_id = $data->product_id;

    try {
        // add product ro wishlist 
        $stmt = $pdo->prepare("INSERT INTO wishlist_entries (session_id, product_id) VALUES (:session_id, :product_id)");
        $stmt->execute(['session_id' => $session_id, 'product_id' => $product_id]);

        echo json_encode(["status" => "success", "message" => "Item added to wishlist"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid input"]);
}
?>
