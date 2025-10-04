<?php
// Teacher Dashboard
session_start();

// Check if the user is logged in and is a teacher
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

// Include header
include '../includes/header.php';
?>

<div class="container">
    <h2>Welcome, Teacher!</h2>
    <p>This is the teacher dashboard.</p>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>