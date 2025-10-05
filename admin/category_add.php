<?php
require_once 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['name'])) {
    $name = trim($_POST['name']);

    $sql = "INSERT INTO categories (name) VALUES (:name)";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
        if ($stmt->execute()) {
            header("location: categories.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
}

include 'includes/header.php';
?>

<h2>Add New Category</h2>
<hr>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="categories.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>