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

// Fetch analytics data
// Example query for sales or any analytics data
$querySales = "SELECT SUM(total_price) AS totalSales FROM Orders WHERE status = 'completed'";
$queryProductsSold = "SELECT COUNT(*) AS productsSold FROM Order_Items";

$resultSales = $conn->query($querySales);
$resultProductsSold = $conn->query($queryProductsSold);

$salesData = $resultSales->fetch_assoc();
$productsSoldData = $resultProductsSold->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Analytics</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
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
    </style>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-gray-900 text-white py-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="amazon_logo.png" alt="Amazon Logo" class="w-32">
                <p class="text-lg font-bold">Admin Analytics</p>
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
                <li><a href="admin_dashboard.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Dashboard</a></li>
                <li><a href="manage_users.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Users</a></li>
                <li><a href="manage_orders.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Orders</a></li>
                <li><a href="manage_products.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Manage Products</a></li>
                <li><a href="analytics.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold active-link">Analytics</a></li>
                <li><a href="settings.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Settings</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow p-6">
            <h1 class="text-3xl font-bold mb-6">Analytics Overview</h1>

            <!-- Analytics Cards -->
            <div class="grid grid-cols-2 gap-6">
                <div class="card text-center">
                    <h3 class="text-xl font-bold mb-2">Total Sales</h3>
                    <p class="text-4xl font-bold text-yellow-500">$<?= number_format($salesData['totalSales'], 2) ?></p>
                </div>
                <div class="card text-center">
                    <h3 class="text-xl font-bold mb-2">Products Sold</h3>
                    <p class="text-4xl font-bold text-yellow-500"><?= $productsSoldData['productsSold'] ?></p>
                </div>
            </div>

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
