// count_items.php
<?php
include 'index.php';

$cart_id = $_GET['cart_id'];

$sql = "SELECT SUM(quantity) as total_items FROM Cart_Items WHERE cart_id = '$cart_id'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo $row['total_items'];
$conn->close();
?>
