<?php
include 'config.php';

$name = $email = $phone = "";
$name_err = $email_err = $phone_err = "";
$id = 0;

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

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
        $sql = "UPDATE students SET name=?, email=?, phone=? WHERE id=?";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $param_name, $param_email, $param_phone, $param_id);

            $param_name = $name;
            $param_email = $email;
            $param_phone = $phone;
            $param_id = $id;

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
} else {
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        $id =  trim($_GET["id"]);
        $sql = "SELECT * FROM students WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $param_id);
            $param_id = $id;

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows == 1) {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $name = $row["name"];
                    $email = $row["email"];
                    $phone = $row["phone"];
                } else {
                    echo "No records found.";
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
        $conn->close();
    } else {
        echo "Invalid request.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Student Information</h2>
        <p>Please edit the input values and submit to update the student record.</p>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
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
            <input type="hidden" name="id" value="<?php echo $id; ?>"/>
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
        </form>
    </div>
</body>
</html>