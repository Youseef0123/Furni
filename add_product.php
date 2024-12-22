<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture_store";

// make connection 
$conn = new mysqli($servername, $username, $password, $dbname);

// check from connection 
if ($conn->connect_error) {
    die("connection failed  " . $conn->connect_error);
}

// check from 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $image_url = $_POST["image_url"];
    $category_id = $_POST["category_id"];

    $sql = "INSERT INTO products (name, description, price, image_url, category_id)
            VALUES ('$name', '$description', '$price', '$image_url', '$category_id')";

    if ($conn->query($sql) === TRUE) {
        echo "product added success";
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
