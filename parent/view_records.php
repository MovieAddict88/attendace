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
$sql = "SELECT s.*, g.grade_name, se.section_name
        FROM students s
        JOIN student_parent sp ON s.id = sp.student_id
        LEFT JOIN grade_levels g ON s.grade_level_id = g.id
        LEFT JOIN sections se ON s.section_id = se.id
        WHERE sp.parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $parent_id);
$stmt->execute();
$students_result = $stmt->get_result();
?>

<div class="main-content">
    <h2>View Child's Record</h2>
    <?php if ($students_result->num_rows > 0): ?>
        <?php while ($student = $students_result->fetch_assoc()): ?>
            <h3><?php echo $student['full_name']; ?></h3>
            <p><strong>Roll Number:</strong> <?php echo $student['roll_number']; ?></p>
            <p><strong>Grade:</strong> <?php echo $student['grade_name']; ?></p>
            <p><strong>Section:</strong> <?php echo $student['section_name']; ?></p>

            <h4>Grades</h4>
            <?php
            $grades_sql = "SELECT subject, quarter, grade FROM grades WHERE student_id = ?";
            $grades_stmt = $conn->prepare($grades_sql);
            $grades_stmt->bind_param('i', $student['id']);
            $grades_stmt->execute();
            $grades_result = $grades_stmt->get_result();
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Quarter</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($grades_result->num_rows > 0): ?>
                        <?php while ($grade = $grades_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $grade['subject']; ?></td>
                                <td><?php echo $grade['quarter']; ?></td>
                                <td><?php echo $grade['grade']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No grades found.</td>
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