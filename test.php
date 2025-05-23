<?php
include 'db.php';
$test_id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'] ?? 'Анонимен';
    $stmt = $pdo->prepare("INSERT INTO results (test_id, user) VALUES (?, ?)");
    $stmt->execute([$test_id, $user]);
    $result_id = $pdo->lastInsertId();

    foreach ($_POST['answers'] as $qid => $answer) {
        $stmt = $pdo->prepare("INSERT INTO answers (result_id, question_id, answer) VALUES (?, ?, ?)");
        $stmt->execute([$result_id, $qid, $answer]);
    }
    header("Location: index.php");
    exit;
}

$questions = $pdo->prepare("SELECT * FROM questions WHERE test_id = ?");
$questions->execute([$test_id]);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Попълване на тест</title>
</head>
<body>
    <h2>Попълни теста</h2>
    <form method="post">
        Име: <input type="text" name="user"><br>
        <hr>
        <?php foreach ($questions as $q): ?>
            <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>
            <?php if ($q['type'] == 'closed'): ?>
                <?php foreach (explode(',', $q['answers']) as $ans): ?>
                    <label><input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($ans) ?>" required> <?= htmlspecialchars($ans) ?></label><br>
                <?php endforeach; ?>
            <?php else: ?>
                <textarea name="answers[<?= $q['id'] ?>]" rows="3" cols="40" required></textarea>
            <?php endif; ?>
            <hr>
        <?php endforeach; ?>
        <button type="submit">Изпрати</button>
    </form>
</body>
</html>
