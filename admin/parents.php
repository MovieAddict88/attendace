<?php
require_once 'header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];

    // The user associated with the parent will be deleted due to CASCADE constraint
    $sql = "DELETE FROM parents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Parent deleted successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting parent.</div>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Parents</h2>
    <a href="parent_add.php" class="btn btn-success">Add New Parent</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT p.id, p.first_name, p.last_name, u.username
                FROM parents p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.last_name, p.first_name";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>
                        <a href='parent_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='parents.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this parent? This will also delete their user account.');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center'>No parents found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>