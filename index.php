<?php include 'header.php'; ?>
<?php include 'config.php'; ?>

<div class="container">
    <h2 class="mt-4">Add New Student</h2>
    <form action="add.php" method="POST" class="mb-4">
        <div class="form-group">
            <input type="text" name="name" class="form-control" placeholder="Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="text" name="phone" class="form-control" placeholder="Phone" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>

    <h2 class="mt-4">Student List</h2>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM students";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "<td>" . $row['email'] . "</td>";
                        echo "<td>" . $row['phone'] . "</td>";
                        echo "<td>";
                        echo "<a href='edit.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='delete.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No students found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>