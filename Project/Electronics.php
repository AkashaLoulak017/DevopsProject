<?php
include 'server.php'; // Include the database connection file
session_start(); // Start session for user authentication

// Check if the user is logged in
$isSignedIn = isset($_SESSION['userName']);
$userName = $isSignedIn ? $_SESSION['userName'] : null;
$userId = $isSignedIn && isset($_SESSION['userId']) ? $_SESSION['userId'] : null;
$role = $isSignedIn && isset($_SESSION['role']) ? $_SESSION['role'] : 'user';

// Handle search query
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : null;

$query = "SELECT * FROM products WHERE category_id = 1";
if ($search_query) {

    $query .= " AND name LIKE '%$search_query%'";
}
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error executing query: " . mysqli_error($conn));
}

// Handle add to cart action
if (isset($_GET['add_to_cart']) && $_GET['add_to_cart'] === 'true' && $isSignedIn && $userId) {
    if (isset($_GET['product_id'])) {
        $product_id = mysqli_real_escape_string($conn, $_GET['product_id']);

        // Check if the product already exists in the cart
        $check_cart_query = "SELECT * FROM cart WHERE user_id = '$userId' AND product_id = '$product_id'";
        $cart_result = mysqli_query($conn, $check_cart_query);

        if (mysqli_num_rows($cart_result) > 0) {
            // Product already in cart, increment quantity
            $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = '$userId' AND product_id = '$product_id'";
            if (!mysqli_query($conn, $update_query)) {
                die("Error updating cart: " . mysqli_error($conn));
            }
        } else {
            // Product not in cart, insert it
            $insert_query = "INSERT INTO cart (user_id, product_id, quantity, added_date) 
                             VALUES ('$userId', '$product_id', 1, CURRENT_TIMESTAMP)";
            if (!mysqli_query($conn, $insert_query)) {
                die("Error adding to cart: " . mysqli_error($conn));
            }
        }

        // Redirect to the same page to refresh cart count
        header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
        exit();
    }
}

// Get the number of items in the cart for the cart icon
$cart_count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = '$userId'";
$cart_count_result = mysqli_query($conn, $cart_count_query);
$cart_count = 0;
if ($cart_count_result && $row = mysqli_fetch_assoc($cart_count_result)) {
    $cart_count = $row['total_items'] ?: 0; // Fallback to 0 if null
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon Clone - Electronics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/js/all.min.js" defer></script>
    <script>
        function isUserSignedIn() {
            return <?= $isSignedIn ? 'true' : 'false' ?>;
        }

        function addToCart(productId) {
            if (!isUserSignedIn()) {
                alert("Please sign in first to add products to your cart.");
                window.location.href = "signin.html";
            } else {
                const url = new URL(window.location.href);
                url.searchParams.set("add_to_cart", "true");
                url.searchParams.set("product_id", productId);
                window.location.href = url.toString();
            }
        }

        function navigateToReturnsAndOrders() {
            if (isUserSignedIn()) {
                window.location.href = 'ReturnsandOrder.php';
            } else {
                window.location.href = 'signin.html';
            }
        }

        function navigateToCart() {
            if (isUserSignedIn()) {
                window.location.href = 'Cart.php';
            } else {
                window.location.href = 'signin.html';
            }
        }
    </script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <!-- Header -->
    <header class="bg-gray-900 text-white">
        <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center space-x-3">
                <div class="w-24">
                    <img src="amazon_logo.png" alt="Amazon Logo">
                </div>
                <p class="text-sm">Deliver to <?php echo "hi",$userId;?><span class="font-bold">Pakistan</span></p>
            </div>
            <div class="flex items-center bg-white rounded-md overflow-hidden w-full max-w-3xl">
                <form action="index.php" method="GET" class="flex w-full">
                    <input type="text" name="search" placeholder="Search Amazon" class="flex-grow px-4 py-2 focus:outline-none">
                    <button type="submit" class="bg-yellow-500 px-4 py-2 text-black hover:bg-yellow-600"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="flex items-center space-x-6">
                <div class="text-sm hover:text-yellow-400 cursor-pointer">
                    <p>Hello, <?= $isSignedIn ? $userName : 'Sign in' ?></p>
                    <?php if ($isSignedIn): ?>
                        <p class="font-bold"><a href="logout.php">Sign Out</a></p>
                    <?php else: ?>
                        <p class="font-bold"><a href="signin.html">Sign In</a></p>
                    <?php endif; ?>
                </div>
                <div class="text-sm hover:text-yellow-400 cursor-pointer" onclick="navigateToReturnsAndOrders()">
                    <p>Returns</p>
                    <p class="font-bold">& Orders</p>
                </div>
                <div class="relative hover:text-yellow-400" onclick="navigateToCart()">
                    <i class="fas fa-cart-plus text-2xl"></i>
                    <span class="absolute top-0 right-0 bg-yellow-500 text-black text-xs rounded-full px-2">
                        <?= htmlspecialchars($cart_count); ?>
                    </span>
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

    <!-- Content Section -->
    <section class="p-6 flex-grow">
        <h2 class="text-xl font-bold mb-4">Electronics</h2>
        <div class="grid grid-cols-4 gap-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="bg-white p-4 rounded-lg shadow-md">
                        <div class="flex justify-center mb-4">
                            <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" class="w-3/4 h-48 object-contain">
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-center"><?php echo $row['name']; ?></h3>
                        <p class="text-yellow-500 font-bold text-center">
                            $<?php echo number_format($row['price'], 2); ?>
                        </p>
                        <button onclick="addToCart(<?php echo $row['product_id']; ?>)" class="mt-4 w-full bg-yellow-500 text-black py-2 rounded-md">
                            Add to Cart
                        </button>
                    </div>
                <?php } ?>
            <?php else: ?>
                <p class="text-center text-gray-500 col-span-4">No products found.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white p-6">
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
