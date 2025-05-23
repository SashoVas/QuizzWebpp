<?php
require __DIR__ . '/../database/db.php';

if (!isset($_POST['test_id'])) {
    header("Location: ../pages/main.php");
    exit;
}

$test_id = $_POST['test_id'];
$reviewer = $_POST['reviewer'];
$result_id = $_POST['result_id'];
$stmt = $pdo->prepare("INSERT INTO reviews (result_id, reviewer) VALUES (?, ?)");
$stmt->execute([$result_id, $reviewer]);
$review_id = $pdo->lastInsertId();

foreach ($_POST['reviews'] as $qid => $data) {
    $stmt = $pdo->prepare("INSERT INTO review_details (review_id, question_id, is_correct, comment) VALUES (?, ?, ?, ?)");
    $stmt->execute([$review_id, $qid, $data['is_correct'], $data['comment']]);
}

header("Location: ../pages/main.php");
?>