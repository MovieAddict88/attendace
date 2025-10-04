<?php
require_once 'header.php';

$username_err = $password_err = "";
$username = $role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username already exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
        $stmt->close();
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    $role = $_POST['role'];

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            // Redirect to user list
            header("location: users.php");
            exit();
        } else {
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
        $stmt->close();
    }
}
?>

<h2>Add New User</h2>
<p>Create a new user account and assign a role.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
        <span class="help-block text-danger"><?php echo $username_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Password</label>
        <input type="password" name="password" class="form-control">
        <span class="help-block text-danger"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group">
        <label>Role</label>
        <select name="role" class="form-control">
            <option value="teacher">Teacher</option>
            <option value="student">Student</option>
            <option value="parent">Parent</option>
            <option value="admin">Admin</option>
        </select>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a href="users.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>