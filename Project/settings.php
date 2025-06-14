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

// Fetch settings data
$querySettings = "SELECT * FROM Settings WHERE setting_id = 1";
$resultSettings = $conn->query($querySettings);
$settingsData = $resultSettings->fetch_assoc();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = 1;
    $name = $_POST['siteName'];
        $email= $_POST['siteEmail'];
        $phone= $_POST['sitePhone'];
       


    $sql = "UPDATE settings SET site_name = ? , site_email=? , site_phone = ? WHERE setting_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $name,$email,$phone, $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: settings.php");
        exit;
    } else {
        $error = "Failed to update the record.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
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
                <p class="text-lg font-bold">Admin Settings</p>
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
                <li><a href="analytics.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold">Analytics</a></li>
                <li><a href="settings.php" class="hover-bg p-4 rounded-lg block text-xl font-semibold active-link">Settings</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow p-6">
            <h1 class="text-3xl font-bold mb-6">Settings</h1>

            <!-- Settings Form -->
            <div class="card">
                <h3 class="text-xl font-bold mb-4">Site Settings</h3>
                <form action="settings.php" method="POST">
                    <div class="mb-4">
                        <label for="siteName" class="block text-lg">Site Name</label>
                        <input type="text" name="siteName" id="siteName" value="<?= $settingsData['site_name'] ?>" class="w-full p-3 border border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="siteEmail" class="block text-lg">Site Email</label>
                        <input type="email" name="siteEmail" id="siteEmail" value="<?= $settingsData['site_email'] ?>" class="w-full p-3 border border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="sitePhone" class="block text-lg">Site Phone</label>
                        <input type="text" name="sitePhone" id="sitePhone" value="<?= $settingsData['site_phone'] ?>" class="w-full p-3 border border-gray-300 rounded-md">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white p-3 rounded-lg">Save Settings</button>
                </form>
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
