<?php
include 'db.php';

if ($_FILES['csv']['tmp_name'] && $_POST['test_name']) {
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
}
header("Location: index.php");
