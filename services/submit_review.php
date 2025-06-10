<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

check_auth_post(['test_id', 'result_id', 'reviews']);
validate_user_roles(['teacher', 'admin']);

$test_id = $_POST['test_id'];
$user_id = $_SESSION['user_id'];
$result_id = $_POST['result_id'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO reviews (result_id, user_id) VALUES (?, ?)");
    $stmt->execute([$result_id, $user_id]);
    $review_id = $pdo->lastInsertId();

    foreach ($_POST['reviews'] as $qid => $data) {
        $stmt = $pdo->prepare("INSERT INTO review_details (review_id, question_id, is_correct, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$review_id, $qid, $data['is_correct'], $data['comment']]);
    }

    $pdo->commit();
    header("Location: ../pages/main.php?message=success");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../pages/review.php?id=$test_id&result_id=$result_id&message=error&error=review");
    exit;
}
?>