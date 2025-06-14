<?php
// Include the database connection
include 'server.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // No need to sanitize, will be hashed
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Start a session and store user information
            session_start();
            $_SESSION['userId'] = $user['user_id']; // Store user_id in session
            $_SESSION['userName'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($role === 'admin') {
                header('Location: admin_dashboard.php'); // Admin-specific dashboard
            } else {
                header('Location: index.php'); // User-specific homepage
            }
            exit; // Stop script execution after the redirect
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found or incorrect role.";
    }
} else {
    echo "Invalid request.";
}
?>
