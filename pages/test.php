<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../helpers/auth_helpers.php';
    require __DIR__ . '/../helpers/message_visualizer.php';

    check_auth_get(['id']);

    $test_id = $_GET['id'];

    $questions = $pdo->prepare("SELECT * FROM questions WHERE test_id = ?");
    $questions->execute([$test_id]);

    if ($questions->rowCount() === 0) {
        header("Location: ../pages/main.php?message=error&error=bad_request");
        exit;
    }

    // Load previous form inputs if available
    $form_inputs = $_SESSION['form_inputs'] ?? [];
    unset($_SESSION['form_inputs']);
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

    <?php visualize_message(); ?>
    
    <form method="post" action="../services/save_answers.php">
        <input type="hidden" name="test_id" value="<?= $test_id ?>">
        Име: <input type="text" name="user" value="<?= htmlspecialchars($form_inputs['user'] ?? '') ?>" required><br>
        <hr>

        <?php foreach ($questions as $q): ?>
            <p><strong><?= htmlspecialchars($q['question']) ?></strong></p>
            <?php if ($q['type'] == 'closed'): ?>
                <?php foreach (explode(',', $q['answers']) as $ans): ?>
                    <label>
                        <input type="radio" name="answers[<?= $q['id'] ?>]" value="<?= htmlspecialchars($ans) ?>" required
                            <?php if (($form_inputs['answers'][$q['id']] ?? '') === $ans) echo 'checked'; ?>>
                        <?= htmlspecialchars($ans) ?>
                    </label><br>
                <?php endforeach; ?>
            <?php else: ?>
                <textarea name="answers[<?= $q['id'] ?>]" rows="3" cols="40" required><?= htmlspecialchars($form_inputs['answers'][$q['id']] ?? '') ?></textarea>
            <?php endif; ?>
            <hr>
        <?php endforeach; ?>

        <button type="submit">Изпрати</button>
    </form>
</body>
</html>
