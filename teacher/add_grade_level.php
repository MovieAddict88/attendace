<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $grade_name = $_POST['grade_name'];

    $sql = "INSERT INTO grade_levels (grade_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $grade_name);

    if ($stmt->execute()) {
        $message = "Grade level added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <h2>Add New Grade Level</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label for="grade_name">Grade Level Name</label>
            <input type="text" name="grade_name" id="grade_name" required>
        </div>
        <button type="submit">Add Grade Level</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>