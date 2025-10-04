-- IMPORTANT: This script uses a hardcoded password ('password') for the 'student_user'.
-- In a production environment, you should use a strong, unique password and
-- manage it securely.

-- Create the database
CREATE DATABASE IF NOT EXISTS student_db;

-- Use the database
USE student_db;

-- Create the students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL
);

-- Create the database user
CREATE USER IF NOT EXISTS 'student_user'@'localhost' IDENTIFIED BY 'password';

-- Grant privileges to the user
GRANT ALL PRIVILEGES ON student_db.* TO 'student_user'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;