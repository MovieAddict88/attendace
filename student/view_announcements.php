<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$student_id = $_SESSION['student_id'];

// Get student's section to find their teachers
$student_info_sql = "SELECT section_id FROM students WHERE id = ?";
$student_info_stmt = $conn->prepare($student_info_sql);
$student_info_stmt->bind_param('i', $student_id);
$student_info_stmt->execute();
$student_info_result = $student_info_stmt->get_result();
$student_info = $student_info_result->fetch_assoc();
$section_id = $student_info['section_id'];

// Get teacher IDs for the student's section
$teacher_ids = [];
if ($section_id) {
    $teachers_sql = "SELECT teacher_id FROM sections WHERE id = ?";
    $teachers_stmt = $conn->prepare($teachers_sql);
    $teachers_stmt->bind_param('i', $section_id);
    $teachers_stmt->execute();
    $teachers_result = $teachers_stmt->get_result();
    while ($row = $teachers_result->fetch_assoc()) {
        if ($row['teacher_id']) {
            $teacher_ids[] = $row['teacher_id'];
        }
    }
}

// Prepare the query to fetch announcements from admin and relevant teachers
$sql = "SELECT title, content, created_at, 'Admin' as author FROM announcements WHERE user_type = 'admin'";
if (!empty($teacher_ids)) {
    $in_clause = implode(',', array_fill(0, count($teacher_ids), '?'));
    $types = str_repeat('i', count($teacher_ids));
    $sql .= " UNION
              SELECT a.title, a.content, a.created_at, t.full_name as author
              FROM announcements a JOIN teachers t ON a.user_id = t.id
              WHERE a.user_type = 'teacher' AND a.user_id IN ($in_clause)";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($teacher_ids)) {
    $stmt->bind_param($types, ...$teacher_ids);
}
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <h2>View Announcements</h2>
    <?php if ($result->num_rows > 0): ?>
        <?php while($announcement = $result->fetch_assoc()): ?>
            <div class="announcement-item" style="margin-bottom: 1.5rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem;">
                <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                <p><strong>From:</strong> <?php echo htmlspecialchars($announcement['author']); ?></p>
                <p><strong>Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></p>
                <div><?php echo nl2br(htmlspecialchars($announcement['content'])); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No announcements found.</p>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>