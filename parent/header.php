<?php
require_once '../includes/auth.php';

// Check if the user is a parent
if ($user['role'] !== 'parent') {
    // Redirect to the appropriate dashboard
    header('Location: ../index.php');
    exit;
}

// Get the parent's ID from the parents table for use in other pages
$sql_parent_id = "SELECT id FROM parents WHERE user_id = ?";
$stmt_parent_id = $conn->prepare($sql_parent_id);
$stmt_parent_id->bind_param("i", $user_id);
$stmt_parent_id->execute();
$parent_id_result = $stmt_parent_id->get_result();
if ($parent_id_result->num_rows !== 1) {
    die("Error: Could not find a parent profile for the logged-in user.");
}
$parent = $parent_id_result->fetch_assoc();
$parent_id = $parent['id'];
$stmt_parent_id->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parent Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Parent Portal</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
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