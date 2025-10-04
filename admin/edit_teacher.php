<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$message = '';
$teacher_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];

    $sql = "UPDATE teachers SET username = ?, full_name = ?, email = ?, phone_number = ?, address = ?, date_of_birth = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssi', $username, $full_name, $email, $phone_number, $address, $date_of_birth, $teacher_id);

    if ($stmt->execute()) {
        $message = "Teacher updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

$sql = "SELECT * FROM teachers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $teacher_id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();
$stmt->close();
?>

<div class="main-content">
    <h2>Edit Teacher</h2>
    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="input-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="<?php echo $teacher['username']; ?>" required>
        </div>
        <div class="input-group">
            <label for="full_name">Full Name</label>
            <input type="text" name="full_name" id="full_name" value="<?php echo $teacher['full_name']; ?>" required>
        </div>
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo $teacher['email']; ?>" required>
        </div>
        <div class="input-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" name="phone_number" id="phone_number" value="<?php echo $teacher['phone_number']; ?>">
        </div>
        <div class="input-group">
            <label for="address">Address</label>
            <textarea name="address" id="address"><?php echo $teacher['address']; ?></textarea>
        </div>
        <div class="input-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo $teacher['date_of_birth']; ?>">
        </div>
        <button type="submit">Update Teacher</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>