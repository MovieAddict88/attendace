<?php
require_once 'header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: classes.php");
    exit;
}

$class_id_to_edit = $_GET['id'];
$class_name_err = "";
$class_name = "";
$teacher_id = null;

// Fetch class data
$sql = "SELECT name, teacher_id FROM classes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $class_name = $row['name'];
    $teacher_id = $row['teacher_id'];
} else {
    echo "<div class='alert alert-danger'>Class not found.</div>";
    exit;
}
$stmt->close();

// Get a list of teachers
$sql_teachers = "SELECT id, username FROM users WHERE role = 'teacher' ORDER BY username";
$teachers_result = $conn->query($sql_teachers);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate class name
    if (empty(trim($_POST["class_name"]))) {
        $class_name_err = "Please enter a class name.";
    } else {
        $class_name = trim($_POST["class_name"]);
    }

    $new_teacher_id = $_POST['teacher_id'] ? $_POST['teacher_id'] : null;

    if (empty($class_name_err)) {
        $sql = "UPDATE classes SET name = ?, teacher_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $class_name, $new_teacher_id, $class_id_to_edit);

        if ($stmt->execute()) {
            header("location: classes.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
        $stmt->close();
    }
}
?>

<h2>Edit Class</h2>
<p>Modify the class details below.</p>

<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-group <?php echo (!empty($class_name_err)) ? 'has-error' : ''; ?>">
        <label>Class Name</label>
        <input type="text" name="class_name" class="form-control" value="<?php echo htmlspecialchars($class_name); ?>">
        <span class="help-block text-danger"><?php echo $class_name_err; ?></span>
    </div>
    <div class="form-group">
        <label>Assign Teacher</label>
        <select name="teacher_id" class="form-control">
            <option value="">None</option>
            <?php
            if ($teachers_result->num_rows > 0) {
                while ($teacher = $teachers_result->fetch_assoc()) {
                    $selected = ($teacher['id'] == $teacher_id) ? 'selected' : '';
                    echo "<option value='" . $teacher['id'] . "' $selected>" . htmlspecialchars($teacher['username']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Update">
        <a href="classes.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>