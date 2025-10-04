<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header('Location: index.php');
    exit();
}
include_once 'includes/header.php';
?>

<div class="main-content">
    <h2>Teacher Dashboard</h2>
    <p>Welcome to the teacher dashboard. Here you can manage your sections, students, and announcements.</p>
    <!-- More content will be added here -->
</div>

<?php include_once 'includes/footer.php'; ?>