<?php
// Database connection
include 'server.php';
session_start();

// Check if user is signed in
$isSignedIn = isset($_SESSION['userName']);
$userId = $isSignedIn && isset($_SESSION['userId']) ? $_SESSION['userId'] : null;
$userName = $isSignedIn ? $_SESSION['userName'] : null;
$role = $isSignedIn && isset($_SESSION['role']) ? $_SESSION['role'] : 'user';
$cart_count_query = "SELECT SUM(quantity) AS total_items FROM cart WHERE user_id = '$userId'";
$cart_count_result = mysqli_query($conn, $cart_count_query);
$cart_count = 0;
if ($cart_count_result && $row = mysqli_fetch_assoc($cart_count_result)) {
    $cart_count = $row['total_items'] ?: 0; // Fallback to 0 if null
}
// Fetch product categories from the database
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

// Prepare the SQL query based on search term and category filter
$query = "SELECT * FROM ProductCategories WHERE name LIKE ?";
$params = ["%$searchTerm%"];

if ($categoryFilter) {
    $query .= " AND name = ?";
    $params[] = $categoryFilter;
}

$stmt = $conn->prepare($query);
$stmt->bind_param(str_repeat("s", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the number of items in the cart for the current user
$cartItemCount = 0;
if ($isSignedIn) {
    $user_id = $_SESSION['userName']; // Assuming the username is the user ID, you can change it if necessary
    $cartQuery = "SELECT COUNT(*) AS cart_count FROM Cart WHERE user_id = ?";
    $stmtCart = $conn->prepare($cartQuery);
    $stmtCart->bind_param("i", $user_id);
    $stmtCart->execute();
    $cartResult = $stmtCart->get_result();
    $cartData = $cartResult->fetch_assoc();
    $cartItemCount = $cartData['cart_count']; // Get the count of items in the cart
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amazon Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/js/all.min.js" defer></script>

    <!-- Add the custom CSS to fix footer at the bottom -->
    <style>
        /* Ensure the body and html take up the full height */
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Make the body a flex container to align the footer at the bottom */
        body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Make sure the main content section can grow and fill the space */
        main {
            flex-grow: 1;
        }
    </style>

    <script>
    // Check if the user is signed in
    function isUserSignedIn() {
        return <?= $isSignedIn ? 'true' : 'false' ?>;
    }

    // Redirect to the appropriate page based on sign-in status
    function handleNavigation(targetPage) {
        if (!isUserSignedIn()) {
            window.location.href = 'signin.html';
        } else {
            window.location.href = targetPage;
        }
    }

    // Custom navigation for returns and orders, and cart
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
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-900 text-white">
    <div class="flex items-center justify-between px-6 py-4">
        <div class="flex items-center space-x-3">
            <div class="w-24">
                <img src="amazon_logo.png" alt="Amazon Logo">
            </div>
            <p class="text-sm">Deliver to <span class="font-bold">Pakistan</span></p>
        </div>
        <div class="flex items-center bg-white rounded-md overflow-hidden w-full max-w-3xl">
            <!-- Search Form -->
            <form action="index.php" method="GET" class="flex w-full">
                <select name="category" class="bg-gray-200 p-2 text-black text-sm">
                    <option value="">All</option>
                    <option value="Electronics" <?php echo ($categoryFilter == "Electronics") ? 'selected' : ''; ?>>Electronics</option>
                    <option value="Fashion" <?php echo ($categoryFilter == "Fashion") ? 'selected' : ''; ?>>Fashion</option>
                    <option value="Home Appliances" <?php echo ($categoryFilter == "Home Appliances") ? 'selected' : ''; ?>>Home Appliances</option>
                    <option value="Toys" <?php echo ($categoryFilter == "Toys") ? 'selected' : ''; ?>>Toys</option>
                </select>
                <input type="text" name="search" placeholder="Search Amazon" class="flex-grow px-4 py-2 focus:outline-none" value="<?= htmlspecialchars($searchTerm) ?>">
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
    <nav class="bg-gray-700 text-white py-2">
        <div class="flex items-center justify-between px-6">
            <div class="flex items-center space-x-4">
                <div class="toggle-drawer-btn hover:bg-gray-800 px-2 py-1 rounded cursor-pointer" onclick="openDrawer()">
                    <i class="fas fa-bars"></i>
                    <p class="inline">All</p>
                </div>
                <p onclick="window.location.href='TodaysDeal.html'" class="hover:underline cursor-pointer">Today's Deals</p>
                <p onclick="window.location.href='CustomerServices.html'" class="hover:underline cursor-pointer">Customer Service</p>
                <p onclick="window.location.href='GiftCards.html'" class="hover:underline cursor-pointer">Gift Cards</p>
                <p onclick="window.location.href='Sell.html'" class="hover:underline cursor-pointer">Sell</p>
            </div>
            <div>
                <p class="font-bold hover:text-yellow-400 cursor-pointer">Shop deals in Electronics</p>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="p-6">
        <section class="mb-8">
            <h2 class="text-xl font-bold mb-4">Shop by Category</h2>
            <div class="grid grid-cols-4 gap-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div onclick="window.location.href='<?= strtolower($row['name']) ?>.php'" 
                         class="bg-white p-4 rounded-lg shadow-md text-center hover:shadow-lg hover:bg-gray-100 transition-all duration-200 cursor-pointer">
                        <img src="<?= $row['image_url'] ?>" alt="<?= $row['name'] ?>" class="w-full h-33 object-cover mb-2">
                        <p class="font-bold"><?= $row['name'] ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>

        <?php if ($role === 'admin'): ?>
            <!-- Admin Section -->
            <section class="mb-8">
                <h2 class="text-xl font-bold mb-4">Admin Panel</h2>
                <div class="flex items-center space-x-4">
                    <button onclick="window.location.href='add_category.php'" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Add Category
                    </button>
                    <button onclick="window.location.href='manage_products.php'" 
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Manage Products
                    </button>
                </div>
            </section>
        <?php endif; ?>
    </main>

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
