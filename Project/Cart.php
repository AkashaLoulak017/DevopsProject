<?php
// Database connection
include 'server.php';
session_start();

$user_id = $_SESSION['userId'];

// Fetch cart items from the database
$query = "SELECT Cart.*, Products.name AS product_name, Products.image_url AS product_image, Products.price AS product_price 
          FROM Cart 
          INNER JOIN Products ON Cart.product_id = Products.product_id 
          WHERE Cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-gray-900 text-white">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-3">
                <div class="w-24">
                    <img src="amazon_logo.png" alt="Amazon Logo">
                </div>
                <p class="text-sm">Deliver to <span class="font-bold">Pakistan</span></p>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-sm hover:text-yellow-400 cursor-pointer">
                    <p>Hello, <?= htmlspecialchars($_SESSION['userName']) ?></p>
                    <p class="font-bold"><a href="logout.php">Sign Out</a></p>
                </div>
                <div class="text-sm hover:text-yellow-400 cursor-pointer" onclick="navigateToReturnsAndOrders()">
                    <p>Returns</p>
                    <p class="font-bold">& Orders</p>
                </div>
                <div class="relative hover:text-yellow-400" onclick="navigateToCart()">
                    <i class="fas fa-cart-plus text-2xl"></i>
                    <span class="absolute top-0 right-0 bg-yellow-500 text-black text-xs rounded-full px-2">0</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation Bar -->
    <nav class="bg-gray-800 text-white p-2">
        <ul class="flex space-x-7">
            <li><a href="index.php" class="hover:underline">Home</a></li>
            <li><a href="Electronics.php" class="hover:underline">Electronics</a></li>
            <li><a href="HomeAppliances.php" class="hover:underline">Home Appliances</a></li>
            <li><a href="Fashion.php" class="hover:underline">Fashion</a></li>
            <li><a href="Toys.php" class="hover:underline">Toys</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow p-6 container mx-auto">
        <h2 class="text-xl font-bold mb-4">Your Cart</h2>
        <?php if ($result->num_rows > 0): ?>
            <div class="grid grid-cols-1 gap-6">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded shadow flex items-center space-x-4">
                        <img src="<?= htmlspecialchars($row['product_image']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="w-20 h-20 object-cover">
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold"><?= htmlspecialchars($row['product_name']) ?></h3>
                            <p>Quantity: <?= htmlspecialchars($row['quantity']) ?></p>
                            <p>Price: $<?= htmlspecialchars($row['product_price']) ?></p>
                        </div>
                        <form action="update_cart.php" method="POST">
                            <input type="hidden" name="cart_id" value="<?= htmlspecialchars($row['cart_id']) ?>">
                            <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">Remove</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="mt-6">
                <form action="order_now.php" method="POST">
                    <button type="submit" class="bg-yellow-500 text-black px-6 py-2 rounded">Order Now</button>
                </form>
            </div>
        <?php else: ?>
            <p class="text-gray-700">Your cart is empty.</p>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white p-6 mt-auto">
        <div class="grid grid-cols-3 gap-6">
            <div>
                <h4 class="font-bold mb-4">Get to Know Us</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-400 hover:text-yellow-400">About Us</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-yellow-400">Careers</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Make Money with Us</h4>
                <ul class="space-y-2">
                    <li><a href="sell.html" class="text-gray-400 hover:text-yellow-400">Sell on Amazon</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">Help</h4>
                <ul class="space-y-2">
                    <li><a href="CustomerServices.html" class="text-gray-400 hover:text-yellow-400">Customer Service</a></li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
