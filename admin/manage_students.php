<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}
include_once '../config/config.php';
include_once 'includes/header.php';

// Fetch filter data
$teachers = $conn->query("SELECT id, full_name FROM teachers");
$grades = $conn->query("SELECT id, grade_name FROM grade_levels");
$sections = $conn->query("SELECT id, section_name FROM sections");

// Base query
$sql = "SELECT s.id, s.full_name, s.roll_number, g.grade_name, se.section_name
        FROM students s
        LEFT JOIN grade_levels g ON s.grade_level_id = g.id
        LEFT JOIN sections se ON s.section_id = se.id";

// Filter logic
$where_clauses = [];
if (!empty($_GET['teacher_id'])) {
    // This requires joining with sections table to find teacher
    $sql = "SELECT s.id, s.full_name, s.roll_number, g.grade_name, se.section_name
            FROM students s
            LEFT JOIN grade_levels g ON s.grade_level_id = g.id
            LEFT JOIN sections se ON s.section_id = se.id
            WHERE se.teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $_GET['teacher_id']);
} else {
    if (!empty($_GET['grade_id'])) {
        $where_clauses[] = "s.grade_level_id = " . (int)$_GET['grade_id'];
    }
    if (!empty($_GET['section_id'])) {
        $where_clauses[] = "s.section_id = " . (int)$_GET['section_id'];
    }

    if (count($where_clauses) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where_clauses);
    }
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

?>

<div class="main-content">
    <h2>Manage Students</h2>

    <form method="GET" action="" class="filter-form">
        <div class="filter-group">
            <label for="teacher_id">Filter by Teacher:</label>
            <select name="teacher_id" id="teacher_id">
                <option value="">All Teachers</option>
                <?php while ($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?php echo $teacher['id']; ?>" <?php echo (isset($_GET['teacher_id']) && $_GET['teacher_id'] == $teacher['id']) ? 'selected' : ''; ?>>
                        <?php echo $teacher['full_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="grade_id">Filter by Grade:</label>
            <select name="grade_id" id="grade_id">
                <option value="">All Grades</option>
                <?php while ($grade = $grades->fetch_assoc()): ?>
                    <option value="<?php echo $grade['id']; ?>" <?php echo (isset($_GET['grade_id']) && $_GET['grade_id'] == $grade['id']) ? 'selected' : ''; ?>>
                        <?php echo $grade['grade_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="filter-group">
            <label for="section_id">Filter by Section:</label>
            <select name="section_id" id="section_id">
                <option value="">All Sections</option>
                <?php while ($section = $sections->fetch_assoc()): ?>
                    <option value="<?php echo $section['id']; ?>" <?php echo (isset($_GET['section_id']) && $_GET['section_id'] == $section['id']) ? 'selected' : ''; ?>>
                        <?php echo $section['section_name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit">Filter</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Roll Number</th>
                <th>Grade</th>
                <th>Section</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['roll_number']; ?></td>
                        <td><?php echo $row['grade_name']; ?></td>
                        <td><?php echo $row['section_name']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include_once 'includes/footer.php'; ?>

<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
th, td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.filter-form {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
</style>