<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// connection
$conn = new mysqli($servername, $username, $password, $dbname);

// check connection 
if ($conn->connect_error) {
    die("failed " . $conn->connect_error);
}

header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Adding a new product
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = isset($data['name']) ? $data['name'] : null;
    $description = isset($data['description']) ? $data['description'] : null;
    $price = isset($data['price']) ? $data['price'] : null;
    $image_url = isset($data['image_url']) ? $data['image_url'] : null;
    $category_id = isset($data['category_id']) ? $data['category_id'] : null;

    if ($name && $description && $price && $image_url && $category_id) {
        $sql = "INSERT INTO products (name, description, price, image_url, category_id) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsi", $name, $description, $price, $image_url, $category_id);
        
        if ($stmt->execute()) {
            echo json_encode(["success" => "تم إضافة المنتج بنجاح"]);
        } else {
            echo json_encode(["error" => "خطأ في إضافة المنتج: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "الرجاء ملء جميع الحقول"]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Deleting a product
    $data = json_decode(file_get_contents('php://input'), true);
    
    $product_id = isset($data['product_id']) ? $data['product_id'] : null;

    if ($product_id === null) {
        echo json_encode(["error" => "خطأ: يجب تقديم product_id"]);
        exit;
    }

    // Prepare the query to delete the product
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "خطأ في إعداد الاستعلام: " . $conn->error]);
        exit;
    }

    // Bind the parameter
    $stmt->bind_param("i", $product_id);

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(["success" => "تم حذف المنتج بنجاح"]);
    } else {
        echo json_encode(["error" => "خطأ: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
