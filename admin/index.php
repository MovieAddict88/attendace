<?php
require_once 'includes/config.php';

// Fetch total categories
$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt->fetchColumn();

// Fetch total questions
$stmt = $pdo->query("SELECT COUNT(*) FROM questions");
$total_questions = $stmt->fetchColumn();

include 'includes/header.php';
?>

<h2>Admin Dashboard</h2>
<hr>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Categories</h5>
                <p class="card-text"><?php echo $total_categories; ?></p>
                <a href="categories.php" class="btn btn-primary">Manage Categories</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Questions</h5>
                <p class="card-text"><?php echo $total_questions; ?></p>
                <a href="questions.php" class="btn btn-primary">Manage Questions</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>