<?php
require_once '../includes/auth.php';

// Check if the user is a student
if ($user['role'] !== 'student') {
    // Redirect to the appropriate dashboard
    header('Location: ../index.php');
    exit;
}

// Get the student's ID from the students table for use in other pages
$sql_student_id = "SELECT id, class_id FROM students WHERE user_id = ?";
$stmt_student_id = $conn->prepare($sql_student_id);
$stmt_student_id->bind_param("i", $user_id);
$stmt_student_id->execute();
$student_id_result = $stmt_student_id->get_result();
if ($student_id_result->num_rows !== 1) {
    die("Error: Could not find a student profile for the logged-in user.");
}
$student = $student_id_result->fetch_assoc();
$student_id = $student['id'];
$stmt_student_id->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Portal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">Student Portal</a>
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