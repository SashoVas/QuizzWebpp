<?php
require __DIR__ . '/../database/db.php';

if (!isset($_POST['test_id'])) {
    header("Location: ../pages/main.php");
    exit;
}

#Save the result to database and go back to main page
$test_id = $_POST['test_id'];
$user = $_POST['user'];
$stmt = $pdo->prepare("INSERT INTO results (test_id, user) VALUES (?, ?)");
$stmt->execute([$test_id, $user]);
$result_id = $pdo->lastInsertId();

foreach ($_POST['answers'] as $qid => $answer) {
    $stmt = $pdo->prepare("INSERT INTO answers (result_id, question_id, answer) VALUES (?, ?, ?)");
    $stmt->execute([$result_id, $qid, $answer]);
}

header("Location: ../pages/main.php"); 
?>