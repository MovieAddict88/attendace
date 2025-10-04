<?php
// Student Dashboard
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Include header
include '../includes/header.php';
?>

<div class="container">
    <h2>Welcome, Student!</h2>
    <p>This is the student dashboard.</p>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>