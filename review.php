<?php
include 'db.php';
$test_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewer = $_POST['reviewer'];
    $result_id = $_POST['result_id'];
    $stmt = $pdo->prepare("INSERT INTO reviews (result_id, reviewer) VALUES (?, ?)");
    $stmt->execute([$result_id, $reviewer]);
    $review_id = $pdo->lastInsertId();

    foreach ($_POST['reviews'] as $qid => $data) {
        $stmt = $pdo->prepare("INSERT INTO review_details (review_id, question_id, is_correct, comment) VALUES (?, ?, ?, ?)");
        $stmt->execute([$review_id, $qid, $data['is_correct'], $data['comment']]);
    }
    header("Location: index.php");
    exit;
}

$results = $pdo->prepare("SELECT * FROM results WHERE test_id = ?");
$results->execute([$test_id]);
?>
<!DOCTYPE html>
<html>
<head><title>Рецензия</title></head>
<body>
    <h2>Избери резултат за рецензия</h2>
    <ul>
        <?php foreach ($results as $r): ?>
            <li>
                <?= $r['user'] ?> - 
                <a href="?id=<?= $test_id ?>&result_id=<?= $r['id'] ?>">Рецензирай</a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php if (isset($_GET['result_id'])):
    $rid = $_GET['result_id'];
    $questions = $pdo->prepare("SELECT q.*, a.answer FROM questions q JOIN answers a ON q.id = a.question_id WHERE a.result_id = ?");
    $questions->execute([$rid]);
?>
<form method="post">
    <input type="hidden" name="result_id" value="<?= $rid ?>">
    Рецензент: <input type="text" name="reviewer" required>
    <hr>
    <?php foreach ($questions as $q): ?>
        <p><strong><?= $q['question'] ?></strong></p>
        <p>Отговор: <?= htmlspecialchars($q['answer']) ?></p>
        Верен ли е?
        <label><input type="radio" name="reviews[<?= $q['id'] ?>][is_correct]" value="1" required> Да</label>
        <label><input type="radio" name="reviews[<?= $q['id'] ?>][is_correct]" value="0"> Не</label><br>
        Коментар:<br>
        <textarea name="reviews[<?= $q['id'] ?>][comment]" rows="2" cols="50"></textarea>
        <hr>
    <?php endforeach; ?>
    <button type="submit">Запиши рецензия</button>
</form>
<?php endif; ?>
</body>
</html>
