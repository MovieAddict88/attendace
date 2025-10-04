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

// 4. Handle form submission to save grades
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grades'])) {
    $subject_id = $_POST['subject_id'];
    $grades = $_POST['grades'] ?? [];

    $conn->begin_transaction();
    try {
        $sql_upsert = "INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)
                       ON DUPLICATE KEY UPDATE grade = VALUES(grade)";
        $stmt_upsert = $conn->prepare($sql_upsert);

        foreach ($grades as $student_id => $grade) {
            // Only save non-empty grades
            if (trim($grade) !== '') {
                $stmt_upsert->bind_param("iis", $student_id, $subject_id, $grade);
                $stmt_upsert->execute();
            } else {
                // Optionally, delete grade if it's cleared
                $sql_delete = "DELETE FROM grades WHERE student_id = ? AND subject_id = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("ii", $student_id, $subject_id);
                $stmt_delete->execute();
                $stmt_delete->close();
            }
        }
        $stmt_upsert->close();
        $conn->commit();
        echo "<div class='alert alert-success'>Grades saved successfully.</div>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error saving grades: " . $e->getMessage() . "</div>";
    }
}

// 5. Determine which subject to show
$selected_subject_id = $_REQUEST['subject_id'] ?? ($subjects_result->num_rows > 0 ? $subjects_result->fetch_assoc()['id'] : null);
$subjects_result->data_seek(0); // Reset pointer

// 6. Fetch existing grades for the selected subject
$existing_grades = [];
if ($selected_subject_id) {
    $sql_grades = "SELECT student_id, grade FROM grades WHERE subject_id = ?";
    $stmt_grades = $conn->prepare($sql_grades);
    $stmt_grades->bind_param("i", $selected_subject_id);
    $stmt_grades->execute();
    $grades_result = $stmt_grades->get_result();
    while ($row = $grades_result->fetch_assoc()) {
        $existing_grades[$row['student_id']] = $row['grade'];
    }
    $stmt_grades->close();
}
?>

<h2>Manage Grades for: <?php echo htmlspecialchars($class_name); ?></h2>
<p>Select a subject to enter or update student grades.</p>

<!-- Form to select subject -->
<form action="grades.php" method="get" class="form-inline mb-4">
    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
    <div class="form-group mr-2">
        <label for="subject_id" class="mr-2">Subject:</label>
        <select name="subject_id" id="subject_id" class="form-control" onchange="this.form.submit()" required>
            <?php if ($subjects_result->num_rows > 0) {
                while ($subject = $subjects_result->fetch_assoc()) {
                    echo "<option value='" . $subject['id'] . "' " . ($subject['id'] == $selected_subject_id ? 'selected' : '') . ">" . htmlspecialchars($subject['name']) . "</option>";
                }
            } else {
                echo "<option value='' disabled>No subjects found for this class</option>";
            } ?>
        </select>
    </div>
</form>

<?php if ($students_result->num_rows > 0 && $selected_subject_id): ?>
    <hr>
    <form action="grades.php?class_id=<?php echo $class_id; ?>" method="post">
        <input type="hidden" name="subject_id" value="<?php echo $selected_subject_id; ?>">
        <input type="hidden" name="save_grades" value="1">

        <table class="table table-striped">
            <thead class="thead-light">
                <tr>
                    <th>Student Name</th>
                    <th style="width: 150px;">Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students_result->fetch_assoc()):
                    $student_id = $student['id'];
                    $grade = $existing_grades[$student_id] ?? '';
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td>
                            <input type="text" name="grades[<?php echo $student_id; ?>]" class="form-control" value="<?php echo htmlspecialchars($grade); ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Save Grades</button>
    </form>
<?php elseif (!$selected_subject_id): ?>
    <div class="alert alert-warning">This class has no subjects. Please ask an administrator to add one.</div>
<?php else: ?>
    <div class="alert alert-info">This class has no students assigned to it.</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>