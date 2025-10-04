<?php
require_once 'header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: users.php");
    exit;
}

$user_id_to_edit = $_GET['id'];
$username_err = $password_err = "";
$username = $role = "";

// Fetch user data
$sql = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $role = $row['role'];
} else {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}
$stmt->close();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username is changed and if the new one is taken
        if (trim($_POST['username']) !== $username) {
            $sql = "SELECT id FROM users WHERE username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_POST["username"]);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
            $stmt->close();
        }
    }

    $role = $_POST['role'];
    $password = $_POST['password'];

    // Validate password if it's being changed
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $password_err = "Password must have at least 6 characters.";
        }
    }

    // Update the database if no errors
    if (empty($username_err) && empty($password_err)) {
        if (!empty($password)) {
            // Update with new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET username = ?, role = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $username, $role, $hashed_password, $user_id_to_edit);
        } else {
            // Update without changing password
            $sql = "UPDATE users SET username = ?, role = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $role, $user_id_to_edit);
        }

        if ($stmt->execute()) {
            header("location: users.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
        $stmt->close();
    }
}
?>

<h2>Edit User</h2>
<p>Modify user details below. Leave the password field blank to keep the current password.</p>

<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
        <span class="help-block text-danger"><?php echo $username_err; ?></span>
    </div>
    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="teacher" <?php echo ($role == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
            <option value="student" <?php echo ($role == 'student') ? 'selected' : ''; ?>>Student</option>
            <option value="parent" <?php echo ($role == 'parent') ? 'selected' : ''; ?>>Parent</option>
            <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
        </select>
    </div>
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>New Password</label>
        <input type="password" name="password" class="form-control">
        <span class="help-block text-danger"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Update">
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>