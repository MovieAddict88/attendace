<?php
session_start();
if (!isset($_SESSION['teacher_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $section_name = $_POST['section_name'];
    $grade_level_id = $_POST['grade_level_id'];
    $teacher_id = $_SESSION['teacher_id'];

    $sql = "INSERT INTO sections (section_name, grade_level_id, teacher_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sii', $section_name, $grade_level_id, $teacher_id);

    if ($stmt->execute()) {
        $message = "Section added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$grades = $conn->query("SELECT id, grade_name FROM grade_levels");
?>

<div class="main-content">
    <h2>Add New Section</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label for="section_name">Section Name</label>
            <input type="text" name="section_name" id="section_name" required>
        </div>
        <div class="input-group">
            <label for="grade_level_id">Grade Level</label>
            <select name="grade_level_id" id="grade_level_id" required>
                <option value="">Select Grade Level</option>
                <?php while ($grade = $grades->fetch_assoc()): ?>
                    <option value="<?php echo $grade['id']; ?>"><?php echo $grade['grade_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit">Add Section</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>