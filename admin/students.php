<?php
require_once 'header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];

    // The user associated with the student will be deleted due to CASCADE constraint
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Student deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting student.</div>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Students</h2>
    <a href="student_add.php" class="btn btn-success">Add New Student</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Class</th>
            <th>Parent</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT s.id, s.first_name, s.last_name, c.name AS class_name,
                       p.first_name AS parent_fname, p.last_name AS parent_lname, u.username
                FROM students s
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN parents p ON s.parent_id = p.id
                JOIN users u ON s.user_id = u.id
                ORDER BY s.last_name, s.first_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['class_name'] ?: 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['parent_fname'] . ' ' . $row['parent_lname']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>
                        <a href='student_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='students.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this student? This will also delete their user account.');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='text-center'>No students found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>