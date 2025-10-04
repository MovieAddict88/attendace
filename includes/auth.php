<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: ../login.php');
    exit;
}

// Include the database configuration
require_once '../config.php';

// Create a database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user's ID and role from the session
$user_id = $_SESSION['user_id'];
$sql = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if the user exists
if (!$user) {
    // If no user is found, destroy the session and redirect to login
    session_destroy();
    header('Location: ../login.php');
    exit;
}
?>