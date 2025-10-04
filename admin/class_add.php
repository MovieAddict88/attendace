<?php
require_once 'header.php';

$class_name = "";
$teacher_id = null;
$class_name_err = "";

// Get a list of teachers to populate the dropdown
$sql_teachers = "SELECT id, username FROM users WHERE role = 'teacher' ORDER BY username";
$teachers_result = $conn->query($sql_teachers);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate class name
    if (empty(trim($_POST["class_name"]))) {
        $class_name_err = "Please enter a class name.";
    } else {
        $class_name = trim($_POST["class_name"]);
    }

    $teacher_id = $_POST['teacher_id'] ? $_POST['teacher_id'] : null;

    if (empty($class_name_err)) {
        // Use a prepared statement to prevent SQL injection
        $sql = "INSERT INTO classes (name, teacher_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind parameters. Use "si" for string and integer. If teacher_id is null, it should be handled correctly.
        $stmt->bind_param("si", $class_name, $teacher_id);

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

<h2>Add New Class</h2>
<p>Create a new class and assign a teacher.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group <?php echo (!empty($class_name_err)) ? 'has-error' : ''; ?>">
        <label>Class Name</label>
        <input type="text" name="class_name" class="form-control" value="<?php echo $class_name; ?>">
        <span class="help-block text-danger"><?php echo $class_name_err; ?></span>
    </div>
    <div class="form-group">
        <label>Assign Teacher</label>
        <select name="teacher_id" class="form-control">
            <option value="">None</option>
            <?php
            if ($teachers_result->num_rows > 0) {
                while ($teacher = $teachers_result->fetch_assoc()) {
                    echo "<option value='" . $teacher['id'] . "'>" . htmlspecialchars($teacher['username']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a href="classes.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>