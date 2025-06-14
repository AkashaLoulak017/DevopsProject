<?php
// Database connection
include 'server.php';
session_start();

// Check if the user is signed in
if (!isset($_SESSION['userName'])) {
    header('Location: signin.html');  // Redirect to sign-in page if the user is not logged in
    exit();
}

$user_id = $_SESSION['userId'];

// Fetch orders from the database
$query = "SELECT Orders.*, 
                 (SELECT SUM(order_items.price * order_items.quantity) 
                  FROM order_items 
                  WHERE order_items.order_id = Orders.order_id) AS total_price
          FROM Orders 
          WHERE Orders.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch order items when an order is clicked (AJAX or redirect to another page)
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $query_items = "SELECT order_items.*, Products.name AS product_name, Products.image_url AS product_image 
                    FROM order_items 
                    INNER JOIN Products ON order_items.product_id = Products.product_id 
                    WHERE order_items.order_id = ?";
    $stmt_items = $conn->prepare($query_items);
    $stmt_items->bind_param("i", $order_id);
    $stmt_items->execute();
    $order_items_result = $stmt_items->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns and Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/js/all.min.js" defer></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Header and Navigation (same as your current code) -->

    <!-- Main Content -->
    <main class="flex-grow p-6 container mx-auto">
        <h2 class="text-xl font-bold mb-4">Your Orders</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 gap-6">
                <?php $order_count = 1; ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow cursor-pointer" onclick="showOrderDetails(<?= $row['order_id'] ?>)">
                        <div class="flex items-center space-x-4">
                            <img src="order.jpg" alt="Order Image" class="w-20 h-20 object-cover">
                            <div>
                                <h3 class="text-lg font-bold">Order <?= $order_count ?></h3>
                                <p>Total Price: $<?= number_format($row['total_price'], 2) ?></p>
                                <p>Order Date: <?= $row['order_date'] ?></p>
                                <p>Status: <?= $row['status'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php $order_count++; ?>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-700">You have no orders.</p>
        <?php endif; ?>

        <!-- Order Items (hidden initially, shown when an order is clicked) -->
        <?php if (isset($order_items_result)): ?>
            <div class="mt-6">
                <h3 class="text-xl font-bold mb-4">Order Items</h3>
                <div class="grid grid-cols-1 gap-6">
                    <?php while ($item = $order_items_result->fetch_assoc()): ?>
                        <div class="bg-white p-4 rounded shadow">
                            <div class="flex items-center space-x-4">
                                <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-20 h-20 object-cover">
                                <div>
                                    <h3 class="text-lg font-bold"><?= htmlspecialchars($item['product_name']) ?></h3>
                                    <p>Quantity: <?= $item['quantity'] ?></p>
                                    <p>Price: $<?= number_format($item['price'], 2) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer (same as your current code) -->

    <script>
        // JavaScript function to show order details when an order is clicked
        function showOrderDetails(orderId) {
            window.location.href = "?order_id=" + orderId;  // Reload the page with the order_id parameter to fetch order items
        }
    </script>
</body>
</html>
