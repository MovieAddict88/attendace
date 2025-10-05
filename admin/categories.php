<?php
require_once 'includes/config.php';
include 'includes/header.php';

// Fetch all categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Manage Categories</h2>
<hr>
<a href="category_add.php" class="btn btn-success mb-3">Add New Category</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $category): ?>
        <tr>
            <td><?php echo htmlspecialchars($category['id']); ?></td>
            <td><?php echo htmlspecialchars($category['name']); ?></td>
            <td>
                <a href="category_edit.php?id=<?php echo $category['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                <a href="category_delete.php?id=<?php echo $category['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>