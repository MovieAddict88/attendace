<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$parent_id = $_SESSION['parent_id'];

// Find the teacher(s) of the parent's child(ren)
$sql = "SELECT DISTINCT t.id, t.full_name
        FROM teachers t
        JOIN sections se ON t.id = se.teacher_id
        JOIN students s ON se.id = s.section_id
        JOIN student_parent sp ON s.id = sp.student_id
        WHERE sp.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$teachers_result = $stmt->get_result();

$teacher_ids = [];
while ($teacher = $teachers_result->fetch_assoc()) {
    $teacher_ids[] = $teacher['id'];
}

$announcements = [];
if (!empty($teacher_ids)) {
    $in_clause = implode(',', array_fill(0, count($teacher_ids), '?'));
    $types = str_repeat('i', count($teacher_ids));

    $announcements_sql = "SELECT a.title, a.content, a.created_at, t.full_name as teacher_name
                          FROM announcements a
                          JOIN teachers t ON a.user_id = t.id
                          WHERE a.user_type = 'teacher' AND a.user_id IN ($in_clause)
                          ORDER BY a.created_at DESC";
    $announcements_stmt = $conn->prepare($announcements_sql);
    $announcements_stmt->bind_param($types, ...$teacher_ids);
    $announcements_stmt->execute();
    $announcements_result = $announcements_stmt->get_result();
    while ($row = $announcements_result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
?>

<div class="main-content">
    <h2>Messages from Teachers</h2>
    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $announcement): ?>
            <div class="message-item" style="margin-bottom: 1.5rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
                <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                <p><strong>From:</strong> <?php echo htmlspecialchars($announcement['teacher_name']); ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></p>
                <div><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No messages found from your child's teachers.</p>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>