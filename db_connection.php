<?php
// Database configuration
$host = 'localhost';     // Your database server (usually localhost)
$db = 'task_master';     // Your database name
$user = 'root';          // Your MySQL username
$pass = '';              // Your MySQL password

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>