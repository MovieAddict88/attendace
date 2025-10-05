<?php
require_once 'includes/config.php';
include 'includes/header.php';

// Fetch all questions with their category names
$sql = "SELECT q.*, c.name AS category_name
        FROM questions q
        JOIN categories c ON q.category_id = c.id
        ORDER BY q.id DESC";
$stmt = $pdo->query($sql);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Questions</h2>
<hr>
<a href="question_add.php" class="btn btn-success mb-3">Add New Question</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Category</th>
            <th>Question</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($questions as $question): ?>
        <tr>
            <td><?php echo htmlspecialchars($question['id']); ?></td>
            <td><?php echo htmlspecialchars($question['category_name']); ?></td>
            <td><?php echo htmlspecialchars($question['question_text']); ?></td>
            <td>
                <a href="question_edit.php?id=<?php echo $question['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="question_delete.php?id=<?php echo $question['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>