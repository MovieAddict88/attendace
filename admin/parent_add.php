<?php
require_once 'header.php';

$username_err = $password_err = $fname_err = $lname_err = "";
$username = $first_name = $last_name = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form data
    if (empty(trim($_POST["first_name"]))) $fname_err = "Please enter a first name.";
    else $first_name = trim($_POST["first_name"]);

    if (empty(trim($_POST["last_name"]))) $lname_err = "Please enter a last name.";
    else $last_name = trim($_POST["last_name"]);

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_POST["username"]);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) $username_err = "This username is already taken.";
        else $username = trim($_POST["username"]);
        $stmt->close();
    }

    if (empty(trim($_POST["password"]))) $password_err = "Please enter a password.";
    elseif (strlen(trim($_POST["password"])) < 6) $password_err = "Password must have at least 6 characters.";
    else $password = trim($_POST["password"]);

    // If no errors, proceed with insertion
    if (empty($fname_err) && empty($lname_err) && empty($username_err) && empty($password_err)) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // 1. Create user account
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'parent';
            $sql_user = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("sss", $username, $hashed_password, $role);
            $stmt_user->execute();
            $new_user_id = $stmt_user->insert_id;
            $stmt_user->close();

            // 2. Create parent record
            $sql_parent = "INSERT INTO parents (user_id, first_name, last_name) VALUES (?, ?, ?)";
            $stmt_parent = $conn->prepare($sql_parent);
            $stmt_parent->bind_param("iss", $new_user_id, $first_name, $last_name);
            $stmt_parent->execute();
            $stmt_parent->close();

            // Commit transaction
            $conn->commit();
            header("location: parents.php");
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
    }
}
?>

<h2>Add New Parent</h2>
<p>Create a new parent profile and user account.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>">
            <span class="help-block text-danger"><?php echo $fname_err; ?></span>
        </div>
        <div class="form-group col-md-6">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>">
            <span class="help-block text-danger"><?php echo $lname_err; ?></span>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block text-danger"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group col-md-6">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block text-danger"><?php echo $password_err; ?></span>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a href="parents.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>