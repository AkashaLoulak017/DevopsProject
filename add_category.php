<?php
// Include the database connection
include 'server.php';
session_start();

// Check if user is signed in (optional, depending on your requirement)
$isSignedIn = isset($_SESSION['userName']);
if (!$isSignedIn) {
    header('Location: signin.html');
    exit();
}

// Initialize error and success messages
$errorMsg = '';
$successMsg = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $imageUrl = $_POST['image_url'] ?? '';

    // Validate input
    if (empty($name) || empty($description) || empty($imageUrl)) {
        $errorMsg = 'All fields are required!';
    } else {
        // Prepare and execute insert query
        $query = "INSERT INTO ProductCategories (name, description, image_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $name, $description, $imageUrl);
        if ($stmt->execute()) {
            $successMsg = 'Category added successfully!';
        } else {
            $errorMsg = 'Error adding category: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header (Optional) -->
    <header class="bg-gray-900 text-white py-4">
        <div class="flex items-center justify-between px-6">
            <div class="text-lg font-bold">Amazon Clone</div>
            <div class="text-sm">
                <a href="index.php" class="text-white">Home</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="p-6">
        <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold mb-4">Add New Category</h1>

            <?php if ($errorMsg): ?>
                <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-md">
                    <?= htmlspecialchars($errorMsg) ?>
                </div>
            <?php elseif ($successMsg): ?>
                <div class="bg-green-100 text-green-700 p-4 mb-4 rounded-md">
                    <?= htmlspecialchars($successMsg) ?>
                </div>
            <?php endif; ?>

            <form action="add_category.php" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-bold text-gray-700">Category Name</label>
                    <input type="text" name="name" id="name" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-bold text-gray-700">Description</label>
                    <textarea name="description" id="description" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md" rows="4" required></textarea>
                </div>

                <div class="mb-4">
                    <label for="image_url" class="block text-sm font-bold text-gray-700">Image URL</label>
                    <input type="text" name="image_url" id="image_url" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md" required>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">Add Category</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer (Optional) -->
    <footer class="bg-gray-900 text-white py-4">
        <div class="flex justify-center">
            <p>&copy; 2025 Amazon Clone. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
