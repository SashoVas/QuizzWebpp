<?php
require __DIR__ . '/../database/db.php';

if (!isset($_GET['id'])) {
    header("Location: ../pages/main.php");
    exit;
}

$test_id = $_GET['id'];

$questions = $pdo->prepare("SELECT * FROM questions WHERE test_id = ?");
$questions->execute([$test_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Попълване на тест</title>
</head>
<body>
    <h2>Попълни теста</h2>
    <form method="post" action="../services/save_answers.php">
        <input type="hidden" name="test_id" value="<?= $test_id ?>">
        <!-- TODO: When users are implemented, remove this field -->
        Име: <input type="text" name="user" required><br>
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
