<?php
require_once 'header.php';

// Fetch the classes assigned to this teacher
$sql_classes = "SELECT id, name FROM classes WHERE teacher_id = ? ORDER BY name";
$stmt_classes = $conn->prepare($sql_classes);
$stmt_classes->bind_param("i", $user_id);
$stmt_classes->execute();
$classes_result = $stmt_classes->get_result();
?>

<h2>Your Dashboard</h2>
<p>Here are the classes you are assigned to. Select a class to manage attendance or grades.</p>

<?php if ($classes_result->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($class = $classes_result->fetch_assoc()): ?>
            <div class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?php echo htmlspecialchars($class['name']); ?></h5>
                    <small>Class ID: <?php echo $class['id']; ?></small>
                </div>
                <p class="mb-1">Use the links below to manage this class.</p>
                <a href="attendance.php?class_id=<?php echo $class['id']; ?>" class="btn btn-info btn-sm">Manage Attendance</a>
                <a href="grades.php?class_id=<?php echo $class['id']; ?>" class="btn btn-secondary btn-sm">Manage Grades</a>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">You are not currently assigned to any classes.</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>