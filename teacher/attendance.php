<?php
require_once 'header.php';

// 1. Get and validate class_id
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    echo "<div class='alert alert-danger'>No class selected. <a href='index.php'>Go back to dashboard.</a></div>";
    require_once 'footer.php';
    exit;
}
$class_id = $_GET['class_id'];

// 2. Security Check: Verify teacher is assigned to this class
$sql_verify = "SELECT name, teacher_id FROM classes WHERE id = ?";
$stmt_verify = $conn->prepare($sql_verify);
$stmt_verify->bind_param("i", $class_id);
$stmt_verify->execute();
$class_result = $stmt_verify->get_result();
if ($class_result->num_rows !== 1) {
    echo "<div class='alert alert-danger'>Class not found.</div>";
    require_once 'footer.php';
    exit;
}
$class = $class_result->fetch_assoc();
if ($class['teacher_id'] != $user_id) {
    echo "<div class='alert alert-danger'>You are not authorized to manage this class.</div>";
    require_once 'footer.php';
    exit;
}
$class_name = $class['name'];
$stmt_verify->close();

// 3. Fetch subjects and students for the class
$subjects_result = $conn->query("SELECT id, name FROM subjects WHERE class_id = $class_id ORDER BY name");
$students_result = $conn->query("SELECT id, first_name, last_name FROM students WHERE class_id = $class_id ORDER BY last_name, first_name");

// 4. Handle form submission to save attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_attendance'])) {
    $subject_id = $_POST['subject_id'];
    $attendance_date = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? [];

    $conn->begin_transaction();
    try {
        // Delete old records for this day/class/subject first
        $sql_delete = "DELETE FROM attendance WHERE class_id = ? AND subject_id = ? AND date = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("iis", $class_id, $subject_id, $attendance_date);
        $stmt_delete->execute();
        $stmt_delete->close();

        // Insert new records
        if (!empty($statuses)) {
            $sql_insert = "INSERT INTO attendance (student_id, class_id, subject_id, status, date) VALUES (?, ?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            foreach ($statuses as $student_id => $status) {
                $stmt_insert->bind_param("iiiss", $student_id, $class_id, $subject_id, $status, $attendance_date);
                $stmt_insert->execute();
            }
            $stmt_insert->close();
        }
        $conn->commit();
        echo "<div class='alert alert-success'>Attendance saved successfully for " . htmlspecialchars($attendance_date) . ".</div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error saving attendance: " . $e->getMessage() . "</div>";
    }
}

// 5. Determine which subject and date to show
$selected_subject_id = $_REQUEST['subject_id'] ?? ($subjects_result->num_rows > 0 ? $subjects_result->fetch_assoc()['id'] : null);
$selected_date = $_REQUEST['date'] ?? date('Y-m-d');
$subjects_result->data_seek(0); // Reset pointer

// 6. Fetch existing attendance data for the selected subject and date
$existing_attendance = [];
if ($selected_subject_id) {
    $sql_att = "SELECT student_id, status FROM attendance WHERE class_id = ? AND subject_id = ? AND date = ?";
    $stmt_att = $conn->prepare($sql_att);
    $stmt_att->bind_param("iis", $class_id, $selected_subject_id, $selected_date);
    $stmt_att->execute();
    $att_result = $stmt_att->get_result();
    while ($row = $att_result->fetch_assoc()) {
        $existing_attendance[$row['student_id']] = $row['status'];
    }
    $stmt_att->close();
}
?>

<h2>Manage Attendance for: <?php echo htmlspecialchars($class_name); ?></h2>
<p>Select a subject and date to view or update attendance records.</p>

<!-- Form to select subject and date -->
<form action="attendance.php" method="get" class="form-inline mb-4">
    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
    <div class="form-group mr-2">
        <label for="subject_id" class="mr-2">Subject:</label>
        <select name="subject_id" id="subject_id" class="form-control" required>
            <?php if ($subjects_result->num_rows > 0) {
                while ($subject = $subjects_result->fetch_assoc()) {
                    echo "<option value='" . $subject['id'] . "' " . ($subject['id'] == $selected_subject_id ? 'selected' : '') . ">" . htmlspecialchars($subject['name']) . "</option>";
                }
            } else {
                echo "<option value='' disabled>No subjects found</option>";
            } ?>
        </select>
    </div>
    <div class="form-group mr-2">
        <label for="date" class="mr-2">Date:</label>
        <input type="date" name="date" id="date" class="form-control" value="<?php echo htmlspecialchars($selected_date); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">View/Edit</button>
</form>

<?php if ($students_result->num_rows > 0 && $selected_subject_id): ?>
    <hr>
    <h3>Attendance for <?php echo htmlspecialchars($selected_date); ?></h3>
    <form action="attendance.php?class_id=<?php echo $class_id; ?>" method="post">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
        <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">
        <input type="hidden" name="save_attendance" value="1">

        <table class="table table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students_result->fetch_assoc()):
                    $student_id = $student['id'];
                    $status = $existing_attendance[$student_id] ?? 'present'; // Default to 'present'
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status[<?php echo $student_id; ?>]" id="present_<?php echo $student_id; ?>" value="present" <?php echo ($status === 'present') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="present_<?php echo $student_id; ?>">Present</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status[<?php echo $student_id; ?>]" id="absent_<?php echo $student_id; ?>" value="absent" <?php echo ($status === 'absent') ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="absent_<?php echo $student_id; ?>">Absent</label>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Save Attendance</button>
    </form>
<?php elseif (!$selected_subject_id): ?>
    <div class="alert alert-warning">This class has no subjects. Please ask an administrator to add one.</div>
<?php else: ?>
    <div class="alert alert-info">This class has no students assigned to it.</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>