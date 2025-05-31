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

// Fetch all products from the database
$query = "SELECT * FROM Products";
$result = $conn->query($query);

// Handle product deletion
if (isset($_GET['delete_id'])) {
    $productId = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM Products WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param('i', $productId);
    $stmt->execute();
    header("Location: manage_products.php"); // Redirect to refresh the page after deletion
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <div class="flex">
        <nav class="w-64 bg-gray-800 text-white p-6">
            <ul class="space-y-4">
                <li><a href="admin_dashboard.php" class="hover:bg-gray-700 p-2 rounded block">Dashboard</a></li>
                <li><a href="manage_users.php" class="hover:bg-gray-700 p-2 rounded block">Manage Users</a></li>
                <li><a href="manage_orders.php" class="hover:bg-gray-700 p-2 rounded block">Manage Orders</a></li>
                <li><a href="manage_products.php" class="hover:bg-gray-700 p-2 rounded block">Manage Products</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow p-6">
            <h1 class="text-3xl font-bold mb-6">Manage Products</h1>
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4 text-left">Product ID</th>
                        <th class="py-2 px-4 text-left">Product Name</th>
                        <th class="py-2 px-4 text-left">Price</th>
                        <th class="py-2 px-4 text-left">Stock</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="py-2 px-4"><?= $row['product_id'] ?></td>
                            <td class="py-2 px-4"><?= $row['name'] ?></td>
                            <td class="py-2 px-4"><?= $row['price'] ?></td>
                            <td class="py-2 px-4"><?= $row['stock'] ?></td>
                            <td class="py-2 px-4">
                                <a href="?delete_id=<?= $row['product_id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>
