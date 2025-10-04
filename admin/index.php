<?php require_once 'header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p>This is the admin dashboard. From here, you can manage all aspects of the school.</p>
        <div class="list-group">
            <a href="users.php" class="list-group-item list-group-item-action">Manage Users</a>
            <a href="classes.php" class="list-group-item list-group-item-action">Manage Classes</a>
            <a href="subjects.php" class="list-group-item list-group-item-action">Manage Subjects</a>
            <a href="students.php" class="list-group-item list-group-item-action">Manage Students</a>
            <a href="parents.php" class="list-group-item list-group-item-action">Manage Parents</a>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>