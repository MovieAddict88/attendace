<?php
$servername = "localhost";
$username = "student_user";
$password = "password";
$dbname = "student_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    // echo "Database created successfully or already exists<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($dbname);

// Create table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50),
    phone VARCHAR(20),
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

if ($conn->query($sql) === TRUE) {
    // echo "Table students created successfully or already exists<br>";
} else {
    echo "Error creating table: " . $conn->error;
}
?>