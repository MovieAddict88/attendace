<?php
session_start();
require_once 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = ?";

if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_role);
    $param_username = $username;
    $param_role = $role;

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $role);
            if (mysqli_stmt_fetch($stmt)) {
                if (password_verify($password, $hashed_password)) {
                    // Password is correct, so start a new session
                    session_start();

                    // Store data in session variables
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["username"] = $username;
                    $_SESSION["role"] = $role;

                    // Redirect user to their respective dashboard
                    header("location: " . $role . "/dashboard.php");
                } else {
                    // Display an error message if password is not valid
                    header("location: login.php?error=invalid_credentials");
                }
            }
        } else {
            // Display an error message if username doesn't exist
            header("location: login.php?error=invalid_credentials");
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>