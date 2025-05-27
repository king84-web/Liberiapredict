<?php

require_once '../db_connection.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'price' => (float)$row['price'],
        'quantity' => (int)$row['quantity']
    ];
}

echo json_encode($products);

mysqli_close($conn);