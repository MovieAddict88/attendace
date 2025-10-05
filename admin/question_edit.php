<?php
require_once 'includes/config.php';

// Fetch categories for the dropdown
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

$question = null;
$id = 0;

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "SELECT * FROM questions WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
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
    $category_id = $_POST['category_id'];
    $question_text = trim($_POST['question_text']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct_option = $_POST['correct_option'];

    $sql = "UPDATE questions SET category_id = :category_id, question_text = :question_text, option1 = :option1, option2 = :option2, option3 = :option3, option4 = :option4, correct_option = :correct_option WHERE id = :id";

    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->bindParam(":category_id", $category_id, PDO::PARAM_INT);
        $stmt->bindParam(":question_text", $question_text, PDO::PARAM_STR);
        $stmt->bindParam(":option1", $option1, PDO::PARAM_STR);
        $stmt->bindParam(":option2", $option2, PDO::PARAM_STR);
        $stmt->bindParam(":option3", $option3, PDO::PARAM_STR);
        $stmt->bindParam(":option4", $option4, PDO::PARAM_STR);
        $stmt->bindParam(":correct_option", $correct_option, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("location: questions.php");
            exit();
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
}

include 'includes/header.php';
?>

<h2>Edit Question</h2>
<hr>
<?php if ($question): ?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $question['category_id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Question Text</label>
        <textarea name="question_text" class="form-control" rows="3" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Option 1</label>
        <input type="text" name="option1" class="form-control" value="<?php echo htmlspecialchars($question['option1']); ?>" required>
    </div>
    <div class="form-group">
        <label>Option 2</label>
        <input type="text" name="option2" class="form-control" value="<?php echo htmlspecialchars($question['option2']); ?>" required>
    </div>
    <div class="form-group">
        <label>Option 3</label>
        <input type="text" name="option3" class="form-control" value="<?php echo htmlspecialchars($question['option3']); ?>" required>
    </div>
    <div class="form-group">
        <label>Option 4</label>
        <input type="text" name="option4" class="form-control" value="<?php echo htmlspecialchars($question['option4']); ?>" required>
    </div>
    <div class="form-group">
        <label>Correct Option Number (1-4)</label>
        <input type="number" name="correct_option" class="form-control" min="1" max="4" value="<?php echo htmlspecialchars($question['correct_option']); ?>" required>
    </div>
    <input type="submit" class="btn btn-primary" value="Update">
    <a href="questions.php" class="btn btn-secondary">Cancel</a>
</form>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>