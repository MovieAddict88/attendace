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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $grade_level_id = $_POST['grade_level_id'];
    $section_id = $_POST['section_id'];
    $file_path = '';

    if (isset($_FILES['assignment_file']) && $_FILES['assignment_file']['error'] == 0) {
        $target_dir = "../uploads/assignments/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = basename($_FILES["assignment_file"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
            $file_path = $target_file;
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }

    if (empty($message)) {
        $sql = "INSERT INTO assignments (title, description, due_date, file_path, teacher_id, grade_level_id, section_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssiii', $title, $description, $due_date, $file_path, $teacher_id, $grade_level_id, $section_id);

        if ($stmt->execute()) {
            $message = "Assignment added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch sections taught by the current teacher
$sections_sql = "SELECT s.id, s.section_name, g.grade_name, s.grade_level_id FROM sections s JOIN grade_levels g ON s.grade_level_id = g.id WHERE s.teacher_id = ?";
$sections_stmt = $conn->prepare($sections_sql);
$sections_stmt->bind_param('i', $teacher_id);
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();
?>

<div class="main-content">
    <h2>Add New Assignment</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="input-group">
            <label for="title">Assignment Title</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="input-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"></textarea>
        </div>
        <div class="input-group">
            <label for="due_date">Due Date</label>
            <input type="date" name="due_date" id="due_date" required>
        </div>
        <div class="input-group">
            <label for="section_id">Section</label>
            <select name="section_id" id="section_id" required onchange="updateGrade(this)">
                <option value="">Select Section</option>
                <?php while ($section = $sections_result->fetch_assoc()): ?>
                    <option value="<?php echo $section['id']; ?>" data-grade-id="<?php echo $section['grade_level_id']; ?>">
                        <?php echo $section['section_name'] . " (" . $section['grade_name'] . ")"; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="input-group">
            <label for="assignment_file">Upload File</label>
            <input type="file" name="assignment_file" id="assignment_file">
        </div>
        <input type="hidden" name="grade_level_id" id="grade_level_id">
        <button type="submit">Add Assignment</button>
    </form>
</div>

<script>
function updateGrade(select) {
    const selectedOption = select.options[select.selectedIndex];
    const gradeId = selectedOption.getAttribute('data-grade-id');
    document.getElementById('grade_level_id').value = gradeId;
}
</script>

<?php include_once 'includes/footer.php'; ?>