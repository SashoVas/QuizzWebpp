<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../services/auth_helpers.php';

check_auth_post(['test_name']);

if (!$_FILES['csv']['tmp_name'] ) {
    header("Location: ../pages/main.php");
    exit;
}

$pdo->beginTransaction();
try {
    $test_name = $_POST['test_name'];
    $stmt = $pdo->prepare("INSERT INTO tests (name) VALUES (?)");
    $stmt->execute([$test_name]);
    $test_id = $pdo->lastInsertId();

    $file = fopen($_FILES['csv']['tmp_name'], 'r');
    while (($data = fgetcsv($file)) !== FALSE) {
        [$question, $type, $answers, $correct] = $data;
        $stmt = $pdo->prepare("INSERT INTO questions (test_id, question, type, answers, correct_answer) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$test_id, $question, $type, $answers, $correct]);
    }
    fclose($file);

    $pdo->commit();
    header("Location: ../pages/main.php?message=success");
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    if (isset($file) && is_resource($file)) {
        fclose($file);
    }
    header("Location: ../pages/main.php?message=error&error=upload");
    exit;
}
?>