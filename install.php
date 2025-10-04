<?php
// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'student_management';
$sql_file = 'database.sql';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $db_name";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($db_name);

// Read the SQL file
$sql_contents = file_get_contents($sql_file);
if ($sql_contents === false) {
    die("Error reading SQL file.");
}

// Execute multi-query
if ($conn->multi_query($sql_contents)) {
    // Discard all results from the multi_query
    while ($conn->next_result()) {
        if (!$conn->more_results()) break;
    }
    echo "Database schema imported successfully.\n";
} else {
    die("Error importing database schema: " . $conn->error);
}

// Close connection
$conn->close();

echo "Installation complete. This file will now be deleted.\n";

// Delete the installation file
unlink(__FILE__);
?>