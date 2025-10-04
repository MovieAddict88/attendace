<?php
// Admin Dashboard
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Include header
include '../includes/header.php';
?>

<div class="container">
    <h2>Welcome, Admin!</h2>
    <p>This is the admin dashboard.</p>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>