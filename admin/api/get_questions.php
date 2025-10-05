<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

if (!isset($_GET['category_id']) || empty($_GET['category_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Category ID is required.']);
    exit;
}

$categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if ($categoryId === false) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid Category ID.']);
    exit;
}

try {
    $sql = "SELECT question_text AS question, option1, option2, option3, option4, correct_option AS answerNr
            FROM questions
            WHERE category_id = :category_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->execute();

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($questions);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>