<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['admin_id'];
    $user_type = 'admin';

    $sql = "INSERT INTO announcements (title, content, user_id, user_type) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssis', $title, $content, $user_id, $user_type);

    if ($stmt->execute()) {
        $message = "Announcement added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<div class="main-content">
    <h2>Add New Announcement</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="input-group">
            <label for="content">Content</label>
            <textarea name="content" id="content" rows="5" required></textarea>
        </div>
        <button type="submit">Add Announcement</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>