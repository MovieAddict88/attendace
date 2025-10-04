<?php
require_once 'header.php';

$subject_name = "";
$class_id = "";
$subject_name_err = "";

// Get a list of classes to populate the dropdown
$sql_classes = "SELECT id, name FROM classes ORDER BY name";
$classes_result = $conn->query($sql_classes);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate subject name
    if (empty(trim($_POST["subject_name"]))) {
        $subject_name_err = "Please enter a subject name.";
    } else {
        $subject_name = trim($_POST["subject_name"]);
    }

    $class_id = $_POST['class_id'];

    if (empty($subject_name_err)) {
        $sql = "INSERT INTO subjects (name, class_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $subject_name, $class_id);

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

<h2>Add New Subject</h2>
<p>Create a new subject and assign it to a class.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group <?php echo (!empty($subject_name_err)) ? 'has-error' : ''; ?>">
        <label>Subject Name</label>
        <input type="text" name="subject_name" class="form-control" value="<?php echo $subject_name; ?>">
        <span class="help-block text-danger"><?php echo $subject_name_err; ?></span>
    </div>
    <div class="form-group">
        <label>Assign to Class</label>
        <select name="class_id" class="form-control" required>
            <option value="" disabled selected>Select a class</option>
            <?php
            if ($classes_result->num_rows > 0) {
                while ($class = $classes_result->fetch_assoc()) {
                    echo "<option value='" . $class['id'] . "'>" . htmlspecialchars($class['name']) . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a href="subjects.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>