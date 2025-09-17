<?php
require 'db_connect.php';

$sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, c.name as category_name, p.image
        FROM products p
        JOIN categories c ON p.category_id = c.id";

$result = $conn->query($sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na consulta SQL: " . $conn->error]);
    exit();
}

$products = [];
$categories = [];

while ($row = $result->fetch_assoc()) {
    $products[] = [
        "id" => $row['id'],
        "name" => $row['name'],
        "description" => $row['description'],
        "price" => $row['price'],
        "category" => $row['category_id'],
        "category_name" => $row['category_name'],
        "image" => $row['image']
    ];
    if (!in_array($row['category_name'], array_column($categories, 'name'))) {
        $categories[] = ["id" => $row['category_id'], "name" => $row['category_name']];
    }
}

echo json_encode([
    "products" => $products,
    "categories" => $categories
]);

$conn->close();
?>
