<?php
include 'header.php';
include 'config.php';

$id = $_GET['id'];
$sql = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
?>

<h2 class="mt-4">Edit Student</h2>
<form action="update.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control" value="<?php echo $row['name']; ?>" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
    </div>
    <div class="form-group">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" value="<?php echo $row['phone']; ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Student</button>
</form>

<?php
$stmt->close();
include 'footer.php';
?>