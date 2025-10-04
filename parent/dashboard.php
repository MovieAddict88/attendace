<?php
// Parent Dashboard
session_start();

// Check if the user is logged in and is a parent
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php");
    exit();
}

// Include header
include '../includes/header.php';
?>

<div class="container">
    <h2>Welcome, Parent!</h2>
    <p>This is the parent dashboard.</p>
    <a href="../logout.php" class="btn btn-danger">Logout</a>
</div>

<?php
// Include footer
include '../includes/footer.php';
?>