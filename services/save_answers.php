<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../services/auth_helpers.php';

check_auth_post(['test_id', 'user', 'answers']);

$test_id = $_POST['test_id'];
$user = $_POST['user'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO results (test_id, user) VALUES (?, ?)");
    $stmt->execute([$test_id, $user]);
    $result_id = $pdo->lastInsertId();

    foreach ($_POST['answers'] as $qid => $answer) {
        $stmt = $pdo->prepare("INSERT INTO answers (result_id, question_id, answer) VALUES (?, ?, ?)");
        $stmt->execute([$result_id, $qid, $answer]);
    }

    $pdo->commit();
    header("Location: ../pages/main.php?message=success");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();

    session_start();
    $_SESSION['form_inputs'] = [
        'user' => $user,
        'answers' => $_POST['answers']
    ];

    header("Location: ../pages/test.php?id=$test_id&error=save");
    exit;
}
?>