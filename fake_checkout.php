<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51QLYVtAg3cRwuUGot7AVHWjY5556JVFDvYP4ssYWPxe7ogRpXGykpGIlffVvZEicgsUrDWqzjioqQHVKfyXBhhqF00c90iiNLW');

// connection to database 
$host = 'localhost';
$dbname = 'furniture_store';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['items']) || empty($data['items'])) {
        echo json_encode(["error" => "No items in the cart"]);
        exit;
    }

    $line_items = [];
    foreach ($data['items'] as $item) {
        $line_items[] = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $item['name'],
                ],
                'unit_amount' => $item['price'] * 100,
            ],
            'quantity' => $item['quantity'],
        ];
    }

    try {
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => 'http://localhost:4200/home',
            'cancel_url' => 'http://localhost/furniture_store/cancel.php',
        ]);

        // insert data into database 
        foreach ($data['items'] as $item) {
            $stmt = $conn->prepare("INSERT INTO orders (product_name, quantity, price) VALUES (?, ?, ?)");
            $stmt->bind_param("sii", $item['name'], $item['quantity'], $item['price']);
            $stmt->execute();
        }

        echo json_encode(['id' => $session->id]);
    } catch (Exception $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
