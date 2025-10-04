<?php
// --- Initial Checks ---

// Check if installation is complete, if not, redirect to install.php
if (!file_exists('config/config.php')) {
    header('Location: install.php');
    exit;
}

// Include configuration and database connection
require_once 'config/config.php';

// Check if install.php still exists and show a warning
$install_warning = '';
if (file_exists('install.php')) {
    $install_warning = '<div class="warning"><strong>Security Warning:</strong> <code>install.php</code> exists. Please delete it immediately.</div>';
}

// --- Login Logic ---
$error_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                throw new Exception("Database connection failed: " . $conn->connect_error);
            }

            // Prepare statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // --- Password Verification ---
                // The default password is 'password'. The hash in database.sql is for 'password'.
                // In a real application, you would use password_hash() during user registration.
                // For this login, we will use a simple check for the default admin for now.
                // A more secure password_verify should be used in a real system.
                // Let's assume the hash in the DB is correct for the password 'password'

                // Hashed password for 'password' using PASSWORD_DEFAULT
                $hashed_password_for_admin = '$2y$10$N.o.l.j.X2lY9q.Ea.V.d.O.p.Q.R.s.T.u.V.w.X.y.Z'; // Example hash

                // Verify password against the hash in the database
                // The hash for the default admin user ('password') is in database.sql
                if (password_verify($password, $user['password'])) {
                    // --- Session Management ---
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $user['role'];

                    // --- Role-based Redirect ---
                    switch ($user['role']) {
                        case 'admin':
                            header('Location: admin/index.php');
                            break;
                        case 'teacher':
                            header('Location: teacher/index.php');
                            break;
                        case 'parent':
                            header('Location: parent/index.php');
                            break;
                        case 'student':
                            header('Location: student/index.php');
                            break;
                        default:
                            $error_message = 'Invalid user role.';
                            break;
                    }
                    exit;
                } else {
                    $error_message = 'Invalid username or password.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }

            $stmt->close();
            $conn->close();

        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Management System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-container { background: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h1 { text-align: center; margin-bottom: 20px; color: #444; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .button { width: 100%; background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .button:hover { background-color: #0056b3; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px; text-align: center; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border: 1px solid #ffeeba; border-radius: 4px; margin-bottom: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>User Login</h1>
        <?php echo $install_warning; ?>
        <?php if ($error_message): ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="button">Login</button>
        </form>
    </div>
</body>
</html>