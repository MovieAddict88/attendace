<?php
require_once '../includes/auth.php';

// Check if the user is a teacher
if ($user['role'] !== 'teacher') {
    // Redirect to the appropriate dashboard
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Teacher Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <!-- Dropdown for classes can be added here if needed -->
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