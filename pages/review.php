<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../services/logout.php';
    require __DIR__ . '/../helpers/message_visualizer.php';

    check_auth_get(['id']);
    validate_user_roles(['teacher', 'admin']);

    $test_id = $_GET['id'];

    $results = $pdo->prepare("SELECT * FROM results WHERE test_id = ?");
    $results->execute([$test_id]);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рецензия</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="../js/form_warning.js"></script>
</head>
<body>
    <?php 
        if ($results->rowCount() !== 0) {
            add_logout_button();
        }
    ?>
    <h2>Избери резултат за рецензия</h2>
    <p><a href="main.php">← Начална страница</a></p>
    <?php 
        if ((!isset($_GET['error'])) && $results->rowCount() === 0) {
            header("Location: ./review.php?id={$test_id}&message=error&error=no_tests");
        }
        visualize_message();
    ?>
    
    <ul>
        <?php foreach ($results as $r): ?>
            <li>
                <?= $r['user'] ?> - 
                <a href="?id=<?= $test_id ?>&result_id=<?= $r['id'] ?>">Рецензирай</a>
            </li>
        <?php endforeach; ?>
    </ul>


<!-- visualize form only if a test is selected -->
<?php 
if (!isset($_GET['result_id'])) {
    echo '</body></html>'; #browser should render page anyway, but add just in case
    exit;
}
$rid = $_GET['result_id'];
$questions = $pdo->prepare("SELECT q.*, a.answer FROM questions q JOIN answers a ON q.id = a.question_id WHERE a.result_id = ?");
$questions->execute([$rid]); 
?> 

<form action="../services/submit_review.php" method="post">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="result_id" value="<?= $rid ?>">
    <input type="hidden" name="test_id" value="<?= $test_id ?>">
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

</body>
</html>
