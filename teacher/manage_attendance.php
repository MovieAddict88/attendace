<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$teacher_id = $_SESSION['teacher_id'];
$message = '';

// Handle form submission to save attendance
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['attendance'])) {
    $attendance_date = $_POST['attendance_date'];
    $section_id = $_POST['section_id'];
    $attendance_data = $_POST['attendance'];

    foreach ($attendance_data as $student_id => $status) {
        // Check if attendance for this student on this date already exists
        $check_sql = "SELECT id FROM attendance WHERE student_id = ? AND attendance_date = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param('is', $student_id, $attendance_date);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Update existing record
            $sql = "UPDATE attendance SET status = ? WHERE student_id = ? AND attendance_date = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sis', $status, $student_id, $attendance_date);
        } else {
            // Insert new record
            $sql = "INSERT INTO attendance (student_id, teacher_id, attendance_date, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiss', $student_id, $teacher_id, $attendance_date, $status);
        }
        $stmt->execute();
        $stmt->close();
        $check_stmt->close();
    }
    $message = "Attendance saved successfully!";
}

// Fetch sections taught by the current teacher
$sections = $conn->prepare("SELECT id, section_name FROM sections WHERE teacher_id = ?");
$sections->bind_param('i', $teacher_id);
$sections->execute();
$sections_result = $sections->get_result();

$students = [];
$selected_section = '';
$selected_date = date('Y-m-d');
if (isset($_GET['section_id']) && isset($_GET['date'])) {
    $selected_section = $_GET['section_id'];
    $selected_date = $_GET['date'];
    $students_sql = "SELECT id, full_name FROM students WHERE section_id = ?";
    $students_stmt = $conn->prepare($students_sql);
    $students_stmt->bind_param('i', $selected_section);
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    while ($row = $students_result->fetch_assoc()) {
        $students[] = $row;
    }
}
?>

<div class="main-content">
    <h2>Manage Daily Attendance</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="GET" action="">
        <div class="input-group">
            <label for="section_id">Select Section:</label>
            <select name="section_id" id="section_id" required>
                <option value="">Select a Section</option>
                <?php while ($section = $sections_result->fetch_assoc()): ?>
                    <option value="<?php echo $section['id']; ?>" <?php echo ($selected_section == $section['id']) ? 'selected' : ''; ?>>
                        <?php echo $section['section_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="input-group">
            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" value="<?php echo $selected_date; ?>" required>
        </div>
        <button type="submit">Load Students</button>
    </form>

    <?php if (!empty($students)): ?>
    <form method="POST" action="">
        <input type="hidden" name="section_id" value="<?php echo $selected_section; ?>">
        <input type="hidden" name="attendance_date" value="<?php echo $selected_date; ?>">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student):
                    // Get existing attendance status if available
                    $status_sql = "SELECT status FROM attendance WHERE student_id = ? AND attendance_date = ?";
                    $status_stmt = $conn->prepare($status_sql);
                    $status_stmt->bind_param('is', $student['id'], $selected_date);
                    $status_stmt->execute();
                    $status_result = $status_stmt->get_result();
                    $current_status = $status_result->fetch_assoc()['status'] ?? 'present';
                ?>
                <tr>
                    <td><?php echo $student['full_name']; ?></td>
                    <td>
                        <label><input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="present" <?php echo ($current_status == 'present') ? 'checked' : ''; ?>> Present</label>
                        <label><input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="absent" <?php echo ($current_status == 'absent') ? 'checked' : ''; ?>> Absent</label>
                        <label><input type="radio" name="attendance[<?php echo $student['id']; ?>]" value="late" <?php echo ($current_status == 'late') ? 'checked' : ''; ?>> Late</label>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <button type="submit">Save Attendance</button>
    </form>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>