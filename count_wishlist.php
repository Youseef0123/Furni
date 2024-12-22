<?php
include 'index.php'; 

// make data as json file 
header('Content-Type: application/json');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// check the request of api
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // to prevent sql injection 
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM wishlist_entries");

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        echo json_encode([
            'status' => 'success',
            'count' => $row['count']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'خطأ: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'the request not type Get'
    ]);
}

$conn->close();
?>
