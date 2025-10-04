<?php
include 'config.php';

$name = $email = $phone = "";
$name_err = $email_err = $phone_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter a name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate phone
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter a phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    if (empty($name_err) && empty($email_err) && empty($phone_err)) {
        $sql = "INSERT INTO students (name, email, phone) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $param_name, $param_email, $param_phone);

            $param_name = $name;
            $param_email = $email;
            $param_phone = $phone;

            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Student</h2>
        <p>Please fill this form to add a new student.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $phone; ?>">
                <span class="invalid-feedback"><?php echo $phone_err; ?></span>
            </div>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </div>
</body>
</html>