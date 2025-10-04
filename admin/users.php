<?php
require_once 'header.php';

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = $_GET['id'];
    // Prevent admin from deleting themselves
    if ($delete_id == $user_id) {
        echo "<div class='alert alert-danger'>You cannot delete your own account.</div>";
    } else {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>User deleted successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error deleting user.</div>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manage Users</h2>
    <a href="user_add.php" class="btn btn-success">Add New User</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT id, username, role FROM users ORDER BY id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                echo "<td>" . htmlspecialchars(ucfirst($row['role'])) . "</td>";
                echo "<td>
                        <a href='user_edit.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='users.php?action=delete&id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No users found</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php require_once 'footer.php'; ?>