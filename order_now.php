<?php
include 'server.php';
session_start();

$user_id = $_SESSION['userId'];

// Fetch cart items
$query = "SELECT Cart.*, Products.price AS product_price 
          FROM Cart 
          INNER JOIN Products ON Cart.product_id = Products.product_id 
          WHERE Cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Calculate total price and prepare data
    $total_price = 0;
    $cart_items = [];
    while ($row = $result->fetch_assoc()) {
        $total_price += $row['quantity'] * $row['product_price'];
        $cart_items[] = $row;
    }
    $productId=
    // Insert into Orders table (do not insert product_id here)
    $query = "INSERT INTO Orders (user_id, total_price, order_date, status) VALUES (?, ?, NOW(), 'Pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("id", $user_id, $total_price);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the last inserted order ID

    // Insert into Order_Items table for each product in the cart
    $query = "INSERT INTO Order_Items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    foreach ($cart_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['product_price'];

        // Check if the product_id exists in the Products table before inserting into Order_Items
        $check_query = "SELECT 1 FROM Products WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Insert the valid product into Order_Items
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $stmt->execute();
        } else {
            // Optionally, log or handle the error if the product does not exist
            // Skip this product as it does not exist in the Products table
            continue;
        }
    }

    // Clear the user's cart after successful order
    $query = "DELETE FROM Cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to a confirmation page
   
    header("Location: cart.php");
    exit();
} else {
    // Redirect back to the cart if there are no items
    header("Location: cart.php");
    exit();
}
?>
