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
    $quiz_date = $_POST['quiz_date'];
    $grade_level_id = $_POST['grade_level_id'];
    $section_id = $_POST['section_id'];

    $sql = "INSERT INTO quizzes (title, description, quiz_date, teacher_id, grade_level_id, section_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssiii', $title, $description, $quiz_date, $teacher_id, $grade_level_id, $section_id);

    if ($stmt->execute()) {
        $message = "Quiz added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch sections and grades taught by the current teacher
$sections_sql = "SELECT s.id, s.section_name, g.grade_name, s.grade_level_id FROM sections s JOIN grade_levels g ON s.grade_level_id = g.id WHERE s.teacher_id = ?";
$sections_stmt = $conn->prepare($sections_sql);
$sections_stmt->bind_param('i', $teacher_id);
$sections_stmt->execute();
$sections_result = $sections_stmt->get_result();
?>

<div class="main-content">
    <h2>Add New Quiz</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label for="title">Quiz Title</label>
            <input type="text" name="title" id="title" required>
        </div>
        <div class="input-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="3"></textarea>
        </div>
        <div class="input-group">
            <label for="quiz_date">Quiz Date</label>
            <input type="date" name="quiz_date" id="quiz_date" required>
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
        <input type="hidden" name="grade_level_id" id="grade_level_id">
        <button type="submit">Add Quiz</button>
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