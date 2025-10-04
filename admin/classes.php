<?php
require_once 'header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $sql = "DELETE FROM classes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Class deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting class. It might be in use.</div>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Classes</h2>
    <a href="class_add.php" class="btn btn-success">Add New Class</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Class Name</th>
            <th>Teacher</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query to get classes and the assigned teacher's username
        $sql = "SELECT c.id, c.name, u.username AS teacher_name
                FROM classes c
                LEFT JOIN users u ON c.teacher_id = u.id
                ORDER BY c.id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . ($row['teacher_name'] ? htmlspecialchars($row['teacher_name']) : 'N/A') . "</td>";
                echo "<td>
                        <a href='class_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='classes.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this class?');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No classes found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>