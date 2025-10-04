<?php
require_once 'header.php'; // Includes auth, session, and student data fetching

// Fetch Student's Grades
$sql_grades = "SELECT s.name AS subject_name, g.grade
               FROM grades g
               JOIN subjects s ON g.subject_id = s.id
               WHERE g.student_id = ?";
$stmt_grades = $conn->prepare($sql_grades);
$stmt_grades->bind_param("i", $student_id);
$stmt_grades->execute();
$grades_result = $stmt_grades->get_result();

// Fetch Student's Attendance Summary
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

// Fetch all subjects for the student's class to display a complete list
$sql_subjects = "SELECT id, name FROM subjects WHERE class_id = ?";
$stmt_subjects = $conn->prepare($sql_subjects);
$stmt_subjects->bind_param("i", $student['class_id']);
$stmt_subjects->execute();
$subjects_result = $stmt_subjects->get_result();
?>

<h3>Your Academic Dashboard</h3>
<p>Here is a summary of your grades and attendance records.</p>

<div class="row">
    <!-- Grades Card -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h4>Grades</h4>
            </div>
            <div class="card-body">
                <?php if ($grades_result->num_rows > 0): ?>
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grades_result->data_seek(0); // Reset pointer before looping
                            while ($grade = $grades_result->fetch_assoc()): ?>
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
        <div class="card mb-4">
            <div class="card-header">
                <h4>Attendance Summary</h4>
            </div>
            <div class="card-body">
                <?php if ($subjects_result->num_rows > 0): ?>
                     <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>Subject</th>
                                <th>Present</th>
                                <th>Absent</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $subjects_result->data_seek(0); // Reset pointer
                            while ($subject = $subjects_result->fetch_assoc()):
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
                     <div class="alert alert-info">You are not assigned to a class with subjects. Attendance cannot be displayed.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>