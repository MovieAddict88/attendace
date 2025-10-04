<?php
require_once 'header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: subjects.php");
    exit;
}

$subject_id_to_edit = $_GET['id'];
$subject_name_err = "";
$subject_name = "";
$class_id = "";

// Fetch subject data
$sql = "SELECT name, class_id FROM subjects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subject_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $subject_name = $row['name'];
    $class_id = $row['class_id'];
} else {
    echo "<div class='alert alert-danger'>Subject not found.</div>";
    exit;
}
$stmt->close();

// Get a list of classes
$sql_classes = "SELECT id, name FROM classes ORDER BY name";
$classes_result = $conn->query($sql_classes);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate subject name
    if (empty(trim($_POST["subject_name"]))) {
        $subject_name_err = "Please enter a subject name.";
    } else {
        $subject_name = trim($_POST["subject_name"]);
    }

    $new_class_id = $_POST['class_id'];

    if (empty($subject_name_err)) {
        $sql = "UPDATE subjects SET name = ?, class_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $subject_name, $new_class_id, $subject_id_to_edit);

        if ($stmt->execute()) {
            header("location: subjects.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
        $stmt->close();
    }
}
?>

<h2>Edit Subject</h2>
<p>Modify the subject details below.</p>

<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-group <?php echo (!empty($subject_name_err)) ? 'has-error' : ''; ?>">
        <label>Subject Name</label>
        <input type="text" name="subject_name" class="form-control" value="<?php echo htmlspecialchars($subject_name); ?>">
        <span class="help-block text-danger"><?php echo $subject_name_err; ?></span>
    </div>
    <div class="form-group">
        <label>Assign to Class</label>
        <select name="class_id" class="form-control" required>
            <?php
            if ($classes_result->num_rows > 0) {
                while ($class = $classes_result->fetch_assoc()) {
                    $selected = ($class['id'] == $class_id) ? 'selected' : '';
                    echo "<option value='" . $class['id'] . "' $selected>" . htmlspecialchars($class['name']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Update">
        <a href="subjects.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>