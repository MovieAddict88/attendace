<?php
require_once 'includes/config.php';

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "DELETE FROM questions WHERE id = :id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("location: questions.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    unset($stmt);
    unset($pdo);
} else {
    // If no id was provided, redirect to questions list
    header("location: questions.php");
    exit();
}
?>