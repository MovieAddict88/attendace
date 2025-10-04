<?php
// Database configuration
// IMPORTANT: In a production environment, you should use environment variables
// or a secure configuration management system instead of hardcoding credentials.
$host = 'localhost';
$username = 'student_user';
$password = 'password';
$database = 'student_db';

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>