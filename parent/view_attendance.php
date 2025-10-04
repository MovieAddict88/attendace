<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$parent_id = $_SESSION['parent_id'];

// Find the student(s) associated with this parent
$sql = "SELECT id, full_name FROM students WHERE id IN (SELECT student_id FROM student_parent WHERE parent_id = ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<div class="main-content">
    <h2>View Attendance</h2>
    <?php if ($students_result->num_rows > 0): ?>
        <?php while ($student = $students_result->fetch_assoc()): ?>
            <h3>Attendance for <?php echo $student['full_name']; ?></h3>
            <?php
            $attendance_sql = "SELECT attendance_date, status FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC";
            $attendance_stmt = $conn->prepare($attendance_sql);
            $attendance_stmt->bind_param('i', $student['id']);
            $attendance_stmt->execute();
            $attendance_result = $attendance_stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendance_result->num_rows > 0): ?>
                        <?php while ($attendance = $attendance_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $attendance['attendance_date']; ?></td>
                                <td><?php echo ucfirst($attendance['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">No attendance records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <hr>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No children associated with this account.</p>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>