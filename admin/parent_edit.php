<?php
require_once 'header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: parents.php");
    exit;
}
$parent_id_to_edit = $_GET['id'];

// Fetch parent data
$sql = "SELECT p.user_id, p.first_name, p.last_name, u.username
        FROM parents p
        JOIN users u ON p.user_id = u.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows != 1) {
    echo "<div class='alert alert-danger'>Parent not found.</div>";
    exit;
}
$parent = $result->fetch_assoc();
$stmt->close();

$username_err = $password_err = $fname_err = $lname_err = "";
$first_name = $parent['first_name'];
$last_name = $parent['last_name'];
$username = $parent['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and update data
    if (empty(trim($_POST["first_name"]))) $fname_err = "Please enter a first name.";
    else $first_name = trim($_POST["first_name"]);

    if (empty(trim($_POST["last_name"]))) $lname_err = "Please enter a last name.";
    else $last_name = trim($_POST["last_name"]);

    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } elseif (trim($_POST["username"]) !== $parent['username']) {
        $sql_check_user = "SELECT id FROM users WHERE username = ?";
        $stmt_check_user = $conn->prepare($sql_check_user);
        $stmt_check_user->bind_param("s", $_POST["username"]);
        $stmt_check_user->execute();
        if ($stmt_check_user->get_result()->num_rows > 0) {
            $username_err = "This username is already taken.";
        } else {
            $username = trim($_POST["username"]);
        }
        $stmt_check_user->close();
    }

    if (!empty(trim($_POST["password"])) && strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    }

    if (empty($fname_err) && empty($lname_err) && empty($username_err) && empty($password_err)) {
        $conn->begin_transaction();
        try {
            // Update users table
            if (!empty(trim($_POST["password"]))) {
                $hashed_password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
                $sql_user = "UPDATE users SET username = ?, password = ? WHERE id = ?";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bind_param("ssi", $username, $hashed_password, $parent['user_id']);
            } else {
                $sql_user = "UPDATE users SET username = ? WHERE id = ?";
                $stmt_user = $conn->prepare($sql_user);
                $stmt_user->bind_param("si", $username, $parent['user_id']);
            }
            $stmt_user->execute();
            $stmt_user->close();

            // Update parents table
            $sql_parent = "UPDATE parents SET first_name = ?, last_name = ? WHERE id = ?";
            $stmt_parent = $conn->prepare($sql_parent);
            $stmt_parent->bind_param("ssi", $first_name, $last_name, $parent_id_to_edit);
            $stmt_parent->execute();
            $stmt_parent->close();

            $conn->commit();
            header("location: parents.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Something went wrong. Please try again later.</div>";
        }
    }
}
?>

<h2>Edit Parent</h2>
<p>Modify the parent's profile and user account details.</p>

<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>">
            <span class="help-block text-danger"><?php echo $fname_err; ?></span>
        </div>
        <div class="form-group col-md-6">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>">
            <span class="help-block text-danger"><?php echo $lname_err; ?></span>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>">
            <span class="help-block text-danger"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group col-md-6">
            <label>New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
            <span class="help-block text-danger"><?php echo $password_err; ?></span>
        </div>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Update">
        <a href="parents.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once 'footer.php'; ?>