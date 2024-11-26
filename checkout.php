<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

$pdo->beginTransaction();
$stmt = $pdo->prepare("INSERT INTO orders (user_id) VALUES (?)");
$stmt->execute([$user_id]);
$order_id = $pdo->lastInsertId();

foreach ($cart as $product_id => $quantity) {
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$order_id, $product_id, $quantity]);
}

$pdo->commit();
unset($_SESSION['cart']);
header("Location: orderConfirmation.php");
?>