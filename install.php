<?php
// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_management";
$sql_file = 'config/database.sql';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Read SQL file
$sql_commands = file_get_contents($sql_file);

if ($sql_commands === false) {
    die("Error reading SQL file.");
}

// Execute multi query
if ($conn->multi_query($sql_commands)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "Tables created successfully from $sql_file";
} else {
    echo "Error creating tables: " . $conn->error;
}

$conn->close();
?>