<?php
require_once 'header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    $sql = "DELETE FROM subjects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Subject deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting subject. It might be in use.</div>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Subjects</h2>
    <a href="subject_add.php" class="btn btn-success">Add New Subject</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Class</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT s.id, s.name AS subject_name, c.name AS class_name
                FROM subjects s
                JOIN classes c ON s.class_id = c.id
                ORDER BY c.name, s.name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                echo "<td>
                        <a href='subject_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='subjects.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this subject?');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No subjects found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>