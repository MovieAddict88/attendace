<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

$student_id = $_SESSION['student_id'];

$sql = "SELECT attendance_date, status FROM attendance WHERE student_id = ? ORDER BY attendance_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main-content">
    <h2>My Attendance Record</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['attendance_date']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2">No attendance records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once 'includes/footer.php'; ?>