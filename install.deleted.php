<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Installation settings ---
$db_file = 'database.sql';

// --- Main installation logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get database credentials from the form
    $db_host = $_POST['db_host'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    $db_name = $_POST['db_name'];

    // --- 1. Create config.php ---
    $config_content = "<?php
define('DB_HOST', '$db_host');
define('DB_USER', '$db_user');
define('DB_PASS', '$db_pass');
define('DB_NAME', '$db_name');
?>";

    if (!file_put_contents('config.php', $config_content)) {
        die("Error: Could not write to config.php. Please check file permissions.");
    }

    // --- 2. Connect to MySQL and create database ---
    $conn = new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_error) {
        unlink('config.php'); // Clean up config file on connection failure
        die("Connection failed: " . $conn->connect_error);
    }

    $sql_create_db = "CREATE DATABASE IF NOT EXISTS `$db_name`";
    if (!$conn->query($sql_create_db)) {
        unlink('config.php');
        die("Error creating database: " . $conn->error);
    }
    $conn->select_db($db_name);

    // --- 3. Import database schema ---
    $sql_schema = file_get_contents($db_file);
    if ($sql_schema === false) {
        die("Error: Cannot read the database.sql file.");
    }

    if (!$conn->multi_query($sql_schema)) {
        die("Error importing database schema: " . $conn->error);
    }

    // Clear multi_query results
    while ($conn->next_result()) {
        if (!$conn->more_results()) break;
    }

    $conn->close();

    // --- 4. Delete install.php and redirect ---
    // Can't delete itself directly in a simple way that works everywhere.
    // We will redirect to a page that can then delete the file.
    // For now, we'll just redirect.
    // A better approach would be to rename it with a .deleted extension
    rename('install.php', 'install.deleted.php');
    header('Location: login.php?install=success');
    exit;
}

// --- HTML Form for installation ---
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Management System - Installation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="login-form" style="margin-top: 50px;">
                    <h2>Installation</h2>
                    <p>Welcome! Please provide your database details to set up the system.</p>
                    <?php if (file_exists('config.php')): ?>
                        <div class="alert alert-warning">
                            <strong>Warning:</strong> <code>config.php</code> already exists. Please delete it to proceed with a fresh installation.
                        </div>
                    <?php else: ?>
                        <form action="install.php" method="post">
                            <div class="form-group">
                                <label>Database Host</label>
                                <input type="text" name="db_host" class="form-control" value="localhost" required>
                            </div>
                            <div class="form-group">
                                <label>Database User</label>
                                <input type="text" name="db_user" class="form-control" value="root" required>
                            </div>
                            <div class="form-group">
                                <label>Database Password</label>
                                <input type="password" name="db_pass" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Database Name</label>
                                <input type="text" name="db_name" class="form-control" value="student_management_system" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Install Now</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>