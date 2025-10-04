<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header('Location: index.php');
    exit();
}
include_once 'includes/header.php';
?>

<div class="main-content">
    <h2>Parent Dashboard</h2>
    <p>Welcome to the parent portal. Here you can view your child's records, attendance, and messages from teachers.</p>
    <!-- More content will be added here -->
</div>

<?php include_once 'includes/footer.php'; ?>