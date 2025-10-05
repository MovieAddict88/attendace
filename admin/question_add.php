<?php
require_once 'includes/config.php';

// Fetch categories for the dropdown
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $question_text = trim($_POST['question_text']);
    $option1 = trim($_POST['option1']);
    $option2 = trim($_POST['option2']);
    $option3 = trim($_POST['option3']);
    $option4 = trim($_POST['option4']);
    $correct_option = $_POST['correct_option'];

    $sql = "INSERT INTO questions (category_id, question_text, option1, option2, option3, option4, correct_option) VALUES (:category_id, :question_text, :option1, :option2, :option3, :option4, :correct_option)";

    if ($stmt = $pdo->prepare($sql)) {
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

<h2>Add New Question</h2>
<hr>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select a category</option>
            <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Question Text</label>
        <textarea name="question_text" class="form-control" rows="3" required></textarea>
    </div>
    <div class="form-group">
        <label>Option 1</label>
        <input type="text" name="option1" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Option 2</label>
        <input type="text" name="option2" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Option 3</label>
        <input type="text" name="option3" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Option 4</label>
        <input type="text" name="option4" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Correct Option Number (1-4)</label>
        <input type="number" name="correct_option" class="form-control" min="1" max="4" required>
    </div>
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="questions.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>