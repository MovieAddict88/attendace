<?php
require_once 'header.php';

// Fetch the children associated with this parent (parent_id is available from header.php)
$sql_children = "SELECT s.id, s.first_name, s.last_name, c.name AS class_name
                 FROM students s
                 LEFT JOIN classes c ON s.class_id = c.id
                 WHERE s.parent_id = ?";
$stmt_children = $conn->prepare($sql_children);
$stmt_children->bind_param("i", $parent_id);
$stmt_children->execute();
$children_result = $stmt_children->get_result();
?>

<h2>Your Dashboard</h2>
<p>Select a child to view their academic progress, including attendance and grades.</p>

<?php if ($children_result->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($child = $children_result->fetch_assoc()): ?>
            <a href="view_child.php?student_id=<?php echo $child['id']; ?>" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1"><?php echo htmlspecialchars($child['first_name'] . ' ' . $child['last_name']); ?></h5>
                </div>
                <p class="mb-1">Class: <?php echo htmlspecialchars($child['class_name'] ?: 'Not assigned'); ?></p>
            </a>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">No children are currently linked to your account.</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>