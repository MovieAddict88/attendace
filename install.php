<?php
// Simple installation script for Student Management System

$config_file = 'config/config.php';
$db_file = 'database/database.sql';
$errors = [];
$success_message = '';

// Check if the config file already exists
if (file_exists($config_file)) {
    die('
        <div style="text-align: center; margin-top: 50px; font-family: sans-serif;">
            <h1>Already Installed!</h1>
            <p>The configuration file <code>config/config.php</code> already exists.</p>
            <p>To reinstall, please delete the <code>config/config.php</code> file and refresh this page.</p>
            <p><a href="login.php">Go to Login</a></p>
        </div>
    ');
}

// Check if the database sql file exists
if (!file_exists($db_file)) {
    die('
        <div style="text-align: center; margin-top: 50px; font-family: sans-serif;">
            <h1>Error!</h1>
            <p>Database schema file <code>database/database.sql</code> not found.</p>
            <p>Please make sure the file exists in the correct directory.</p>
        </div>
    ');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';

    // --- 1. Connect to MySQL and Create Database ---
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }

        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name`";
        if (!$conn->query($sql)) {
            throw new Exception("Error creating database: " . $conn->error);
        }
        $conn->select_db($db_name);

    } catch (Exception $e) {
        $errors[] = "Database setup failed: " . $e->getMessage();
    }

    // --- 2. Write Config File ---
    if (empty($errors)) {
        $config_content = "<?php
// Database Configuration
define('DB_HOST', '{$db_host}');
define('DB_NAME', '{$db_name}');
define('DB_USER', '{$db_user}');
define('DB_PASS', '{$db_pass}');

// Site Configuration
define('BASE_URL', 'http://' . \$_SERVER['HTTP_HOST'] . str_replace('install.php', '', \$_SERVER['SCRIPT_NAME']));

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>";
        if (!file_put_contents($config_file, $config_content)) {
            $errors[] = "Could not write to <code>{$config_file}</code>. Please check file permissions.";
        }
    }

    // --- 3. Import SQL Schema ---
    if (empty($errors)) {
        $sql_content = file_get_contents($db_file);
        // Remove comments and split into individual queries
        $sql_content = preg_replace('/--.*/', '', $sql_content);
        $sql_queries = preg_split('/;(\r\n|\n|\r)/', $sql_content, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($sql_queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                if (!$conn->query($query)) {
                    $errors[] = "Error importing database schema. Query failed: <pre>$query</pre> Error: " . $conn->error;
                    break; // Stop on first error
                }
            }
        }
    }

    if (empty($errors)) {
        $success_message = '
            <h2>Installation Successful!</h2>
            <p>The application has been installed successfully.</p>
            <p><strong>Default Admin Login:</strong></p>
            <ul>
                <li>Username: <strong>admin</strong></li>
                <li>Password: <strong>password</strong></li>
            </ul>
            <p>For security reasons, you should now delete the <code>install.php</code> file.</p>
            <a href="login.php" class="button">Go to Login Page</a>
        ';
    }

    if (isset($conn)) {
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System - Installation</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #444; }
        form { display: flex; flex-direction: column; }
        label { margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .button { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; }
        .button:hover { background-color: #218838; }
        .errors { background: #f8d7da; color: #721c24; padding: 10px; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Management System Installation</h1>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <strong>The following errors occurred:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success">
                <?php echo $success_message; ?>
            </div>
        <?php else: ?>
            <p>Please provide your database details below. The script will create the database and set up the necessary tables.</p>
            <form action="install.php" method="POST">
                <label for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="localhost" required>

                <label for="db_name">Database Name</label>
                <input type="text" id="db_name" name="db_name" value="student_management" required>

                <label for="db_user">Database Username</label>
                <input type="text" id="db_user" name="db_user" value="root" required>

                <label for="db_pass">Database Password</label>
                <input type="password" id="db_pass" name="db_pass">

                <button type="submit" class="button">Install Now</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>