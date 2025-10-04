<?php
// A diagnostic script to test password verification.
require_once 'config.php';

echo "--- Starting Password Verification Test ---\n";

// --- Database Connection ---
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . "\n");
}
echo "Database connection successful.\n";

// --- Credentials to Test ---
$test_users = [
    'admin' => 'admin',
    'testteacher' => 'password',
    'testparent' => 'password',
    'teststudent' => 'password'
];

foreach ($test_users as $username => $plain_password) {
    echo "\n--- Testing user: $username ---\n";

    // 1. Fetch the stored hash
    $sql = "SELECT password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error . "\n";
        continue;
    }
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        echo "Error: User '$username' not found in database.\n";
        $stmt->close();
        continue;
    }

    $row = $result->fetch_assoc();
    $stored_hash = $row['password'];
    $stmt->close();

    echo "Stored Hash: $stored_hash\n";
    echo "Plain Password: $plain_password\n";

    // 2. Verify the password
    if (password_verify($plain_password, $stored_hash)) {
        echo "Result: SUCCESS - Password is correct.\n";
    } else {
        echo "Result: FAILURE - Password does not match.\n";
    }
}

$conn->close();
echo "\n--- Test Complete ---\n";
?>