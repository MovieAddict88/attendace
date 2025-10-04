<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$student_id = $_SESSION['student_id'];

$sql = "SELECT g.subject, g.quarter, g.grade, t.full_name as teacher_name
        FROM grades g
        JOIN teachers t ON g.teacher_id = t.id
        WHERE g.student_id = ?
        ORDER BY g.subject, g.quarter";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main-content">
    <h2>View Final Grades</h2>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Quarter</th>
                <th>Grade</th>
                <th>Teacher</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo $row['quarter']; ?></td>
                        <td><?php echo $row['grade']; ?></td>
                        <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No grades found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once 'includes/footer.php'; ?>