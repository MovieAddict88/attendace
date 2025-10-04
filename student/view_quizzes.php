<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$student_id = $_SESSION['student_id'];

// Get student's section and grade level
$student_info_sql = "SELECT section_id, grade_level_id FROM students WHERE id = ?";
$student_info_stmt = $conn->prepare($student_info_sql);
$student_info_stmt->bind_param('i', $student_id);
$student_info_stmt->execute();
$student_info_result = $student_info_stmt->get_result();
$student_info = $student_info_result->fetch_assoc();
$section_id = $student_info['section_id'];
$grade_level_id = $student_info['grade_level_id'];

// Fetch quizzes for the student's section and grade
$sql = "SELECT q.title, q.description, q.quiz_date, t.full_name as teacher_name
        FROM quizzes q
        JOIN teachers t ON q.teacher_id = t.id
        WHERE q.section_id = ? AND q.grade_level_id = ?
        ORDER BY q.quiz_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $section_id, $grade_level_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main-content">
    <h2>View Quizzes</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Description</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['quiz_date']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No quizzes found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once 'includes/footer.php'; ?>