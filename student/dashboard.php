<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php');
    exit();
}
include_once 'includes/header.php';
?>

<div class="main-content">
    <h2>Student Dashboard</h2>
    <p>Welcome to the student portal. Here you can view your daily records, quizzes, announcements, assignments, and final grades.</p>
    <!-- More content will be added here -->
</div>

<?php include_once 'includes/footer.php'; ?>