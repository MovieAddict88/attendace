<?php
require_once 'header.php';

// 1. Get and validate student_id from URL
if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
    echo "<div class='alert alert-danger'>No student selected.</div>";
    require_once 'footer.php';
    exit;
}
$student_id = $_GET['student_id'];

// 2. Security Check: Verify this student belongs to the logged-in parent
$sql_verify = "SELECT first_name, last_name, class_id, parent_id FROM students WHERE id = ?";
$stmt_verify = $conn->prepare($sql_verify);
$stmt_verify->bind_param("i", $student_id);
$stmt_verify->execute();
$student_result = $stmt_verify->get_result();
if ($student_result->num_rows !== 1) {
    echo "<div class='alert alert-danger'>Student not found.</div>";
    require_once 'footer.php';
    exit;
}
$student = $student_result->fetch_assoc();
if ($student['parent_id'] != $parent_id) {
    echo "<div class='alert alert-danger'>You are not authorized to view this student's records.</div>";
    require_once 'footer.php';
    exit;
}
$stmt_verify->close();

// 3. Fetch Student's Grades
$sql_grades = "SELECT s.name AS subject_name, g.grade
               FROM grades g
               JOIN subjects s ON g.subject_id = s.id
               WHERE g.student_id = ?";
$stmt_grades = $conn->prepare($sql_grades);
$stmt_grades->bind_param("i", $student_id);
$stmt_grades->execute();
$grades_result = $stmt_grades->get_result();

// 4. Fetch Student's Attendance
$sql_attendance = "SELECT subject_id, status, COUNT(*) AS count
                   FROM attendance
                   WHERE student_id = ?
                   GROUP BY subject_id, status";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("i", $student_id);
$stmt_attendance->execute();
$attendance_result = $stmt_attendance->get_result();

// Process attendance data into a more usable format
$attendance_summary = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_summary[$row['subject_id']][$row['status']] = $row['count'];
}

// Fetch all subjects for the student's class to display full list
$sql_subjects = "SELECT id, name FROM subjects WHERE class_id = ?";
$stmt_subjects = $conn->prepare($sql_subjects);
$stmt_subjects->bind_param("i", $student['class_id']);
$stmt_subjects->execute();
$subjects_result = $stmt_subjects->get_result();

?>

<h3>Academic Report for <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h3>
<a href="index.php" class="btn btn-secondary mb-4">&larr; Back to Dashboard</a>

<div class="row">
    <!-- Grades Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Grades</h4>
            </div>
            <div class="card-body">
                <?php if ($grades_result->num_rows > 0): ?>
                    <table class="table table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($grade = $grades_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($grade['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($grade['grade']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No grades have been recorded yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attendance Card -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4>Attendance Summary</h4>
            </div>
            <div class="card-body">
                <?php if ($subjects_result->num_rows > 0): ?>
                     <table class="table table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th>Present</th>
                                <th>Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($subject = $subjects_result->fetch_assoc()):
                                $subject_id = $subject['id'];
                                $present_count = $attendance_summary[$subject_id]['present'] ?? 0;
                                $absent_count = $attendance_summary[$subject_id]['absent'] ?? 0;
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['name']); ?></td>
                                    <td><?php echo $present_count; ?></td>
                                    <td><?php echo $absent_count; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                     <div class="alert alert-info">No subjects found for the student's class, so attendance cannot be displayed.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>