<?php
include 'index.php'; // Ensure this file contains database connection details

$sql = "SELECT * FROM Products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Display the data
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["product_id"]. " - Name: " . $row["name"]. " - Price: " . $row["price"]. "<br>";
    }
} else {
    echo "No data available";
}

$conn->close();
?>
