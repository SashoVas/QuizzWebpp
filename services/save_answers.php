<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

check_auth_post(['test_id', 'answers']);
validate_user_roles(['student', 'admin']);

$test_id = $_POST['test_id'];
$user_id = $_SESSION['user_id'];

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("INSERT INTO results (test_id, user_id) VALUES (?, ?)");
    $stmt->execute([$test_id, $user_id]);
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
        'user_id' => $user_id,
        'answers' => $_POST['answers']
    ];

    header("Location: ../pages/test.php?id=$test_id&message=error&error=save");
    exit;
}
?>