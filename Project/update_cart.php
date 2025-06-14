<?php
include 'server.php';
session_start();

$cart_id = $_POST['cart_id'];
$quantity = $_POST['quantity'];

if ($quantity > 1) {
    // Decrement quantity
    $query = "UPDATE Cart SET quantity = quantity - 1 WHERE cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
} else {
    // Remove item from cart
    $query = "DELETE FROM Cart WHERE cart_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
}

header("Location: cart.php");
exit();
?>
