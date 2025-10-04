<?php
require_once '../includes/auth.php';

// Check if the user is an admin
if ($user['role'] !== 'admin') {
    // Redirect to the appropriate dashboard
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Admin Portal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="classes.php">Classes</a></li>
                <li class="nav-item"><a class="nav-link" href="subjects.php">Subjects</a></li>
                <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
                <li class="nav-item"><a class="nav-link" href="parents.php">Parents</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="navbar-text">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">