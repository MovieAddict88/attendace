<?php
require_once 'includes/config.php';

$name = "";
$id = 0;

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "SELECT * FROM categories WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $name = $row["name"];
            } else {
                echo "No record found.";
                exit();
            }
        } else {
            echo "Something went wrong.";
            exit();
        }
    }
    unset($stmt);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $name = trim($_POST["name"]);

    if (!empty($name)) {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("location: categories.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
        unset($stmt);
    }
}

include 'includes/header.php';
?>

<h2>Edit Category</h2>
<hr>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <div class="form-group">
        <label>Category Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
    </div>
    <input type="submit" class="btn btn-primary" value="Update">
    <a href="categories.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>