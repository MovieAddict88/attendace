<?php
// Check if the configuration file exists
if (!file_exists('config.php')) {
    // Redirect to the installation page
    header('Location: install.php');
    exit;
}

// Include the configuration file
require_once 'config.php';

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for a successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: login.php');
    exit;
}

// Get the user's role
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$role = $row['role'];

// Redirect based on the user's role
switch ($role) {
    case 'admin':
        header('Location: admin/index.php');
        break;
    case 'teacher':
        header('Location: teacher/index.php');
        break;
    case 'student':
        header('Location: student/index.php');
        break;
    case 'parent':
        header('Location: parent/index.php');
        break;
    default:
        // Redirect to the login page if the role is not recognized
        header('Location: login.php');
        break;
}
?>