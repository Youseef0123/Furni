<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// الاتصال بقاعدة البيانات
include 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $first_name = isset($data['first_name']) ? $data['first_name'] : null;
    $last_name = isset($data['last_name']) ? $data['last_name'] : null;
    $email = isset($data['email']) ? $data['email'] : null;
    $comment = isset($data['comment']) ? $data['comment'] : null;

    if (!$first_name || !$last_name || !$email || !$comment) {
        echo json_encode(["error" => "All fields are required"]);
        exit;
    }

    // إعداد الاستعلام
    $sql = "INSERT INTO ContactUs (first_name, last_name, email, comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(["error" => "Error preparing query: " . $conn->error]);
        exit;
    }

    // تمرير القيم
    $stmt->bind_param("ssss", $first_name, $last_name, $email, $comment);

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        echo json_encode(["success" => "Data submitted successfully"]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
