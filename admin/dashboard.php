<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
include_once 'includes/header.php';
?>

<div class="main-content">
    <h2>Admin Dashboard</h2>
    <p>Welcome to the admin dashboard. Here you can manage the entire system.</p>
    <!-- More content will be added here -->
</div>

<?php include_once 'includes/footer.php'; ?>