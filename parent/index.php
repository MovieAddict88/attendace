<?php
session_start();

// Check if user is logged in and is a parent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        h1 { color: #333; }
        a { color: #007bff; }
    </style>
</head>
<body>
    <h1>Welcome, Parent!</h1>
    <p>This is your dashboard to view your child's progress.</p>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>