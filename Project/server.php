<?php
$serverName = "localhost"; // Replace with your server name or IP
$username = "root";        // Replace with your MySQL username
$password = "";            // Replace with your MySQL password
$database = "amazonClone";     // Replace with your database name

// Create connection
$conn = mysqli_connect($serverName, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
