<?php
// Include the database connection file
include 'server.php';
session_start();

// Check if the user is an admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
if (!$isAdmin) {
    header("Location: index.php"); // Redirect to home page if not admin
    exit();
}

// Fetch data for dashboard
// Example queries for total orders, total users, etc.
$queryOrders = "SELECT COUNT(*) AS totalOrders FROM Orders";
$queryProducts = "SELECT COUNT(*) AS totalProducts FROM Products";
$queryUsers = "SELECT COUNT(*) AS totalUsers FROM Users";

$resultOrders = $conn->query($queryOrders);
$resultProducts = $conn->query($queryProducts);
$resultUsers = $conn->query($queryUsers);

$orderData = $resultOrders->fetch_assoc();
$productData = $resultProducts->fetch_assoc();
$userData = $resultUsers->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Additional custom styles for a professional look */
        body {
            font-family: 'Arial', sans-serif;
        }
        .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .hover-bg {
            transition: background-color 0.3s ease;
        }
        .hover-bg:hover {
            background-color: #2d3748;
        }
        .active-link {
            background-color: #4c51bf;
        }
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-gray-900 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="amazon_logo.png" alt="Amazon Logo" class="w-32">
                <p class="text-lg font-bold">Admin Dashboard</p>
            </div>
            <div>
                <a href="logout.php" class="text-sm text-yellow-500">Sign Out</a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="flex flex-1">
        <nav class="w-64 bg-gray-800 text-white p-6">
            <ul class="space-y-4">
                <li><a href="admin_dashboard.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold active-link">Dashboard</a></li>
                <li><a href="manage_users.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Users</a></li>
                <li><a href="manage_orders.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Orders</a></li>
                <li><a href="manage_products.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Products</a></li>
                <li><a href="analytics.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Analytics</a></li>
                <li><a href="settings.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Settings</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow p-6">
            <h1 class="text-3xl font-bold mb-6">Dashboard Overview</h1>

            <!-- Overview Cards -->
            <div class="grid grid-cols-3 gap-6">
                <div class="card text-center">
                    <h3 class="text-xl font-bold mb-2">Total Orders</h3>
                    <p class="text-4xl font-bold text-yellow-500"><?= $orderData['totalOrders'] ?></p>
                </div>
                <div class="card text-center">
                    <h3 class="text-xl font-bold mb-2">Total Products</h3>
                    <p class="text-4xl font-bold text-yellow-500"><?= $productData['totalProducts'] ?></p>
                </div>
                <div class="card text-center">
                    <h3 class="text-xl font-bold mb-2">Total Users</h3>
                    <p class="text-4xl font-bold text-yellow-500"><?= $userData['totalUsers'] ?></p>
                </div>
            </div>

            <!-- Quick Links -->
            <section class="mt-8">
                <h2 class="text-xl font-bold mb-4">Quick Links</h2>
                <div class="grid grid-cols-2 gap-6">
                    <a href="manage_products.php" class="card bg-blue-500 text-blue text-center p-6 rounded-lg shadow-md hover:bg-blue-600">
                        Manage Products
                    </a>
                    <a href="manage_orders.php" class="card bg-green-500 text-blue text-center p-6 rounded-lg shadow-md hover:bg-green-600">
                        Manage Orders
                    </a>
                    <a href="manage_users.php" class="card bg-red-500 text-blue text-center p-6 rounded-lg shadow-md hover:bg-red-600">
                        Manage Users
                    </a>
                    <a href="analytics.php" class="card bg-purple-500 text-blue text-center p-6 rounded-lg shadow-md hover:bg-purple-600">
                        View Analytics
                    </a>
                </div>
            </section>

        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-4 mt-auto">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 Amazon Clone. All Rights Reserved.</p>
        </div>
    </footer>

</body>

</html>
