<?php
include 'db.php';

// Проверка за избран тест
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}
$test_id = intval($_GET['id']);

// Извличане на рецензиите за теста
$reviews = $pdo->prepare(
    "SELECT r.id, r.reviewer, r.review_time, u.user 
     FROM reviews r 
     JOIN results u ON r.result_id = u.id 
     WHERE u.test_id = ?
     ORDER BY r.review_time DESC"
);
$reviews->execute([$test_id]);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <title>Преглед на рецензии</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Рецензии на тест</h2>
    <p><a href="index.php">← Начална страница</a></p>
    <ul>
        <?php foreach ($reviews as $r): ?>
            <li>
                <strong>Ревю от:</strong> <?= htmlspecialchars($r['reviewer']) ?>
                <strong>За:</strong> <?= htmlspecialchars($r['user']) ?>
                <em>(<?= $r['review_time'] ?>)</em>
                &nbsp;|&nbsp;
                <a href="view_reviews.php?id=<?= $test_id ?>&review_id=<?= $r['id'] ?>">Прегледай</a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php
// Ако е избрана конкретна рецензия – извличаме детайлите
if (isset($_GET['review_id'])):
    $review_id = intval($_GET['review_id']);
    $stmt = $pdo->prepare(
        "SELECT q.question, d.is_correct, d.comment 
         FROM review_details d 
         JOIN questions q ON d.question_id = q.id 
         WHERE d.review_id = ?"
    );
    $stmt->execute([$review_id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <h3>Детайли на рецензия</h3>
    <?php foreach ($details as $row): ?>
        <div class="review-detail">
            <p><strong>Въпрос:</strong> <?= htmlspecialchars($row['question']) ?></p>
            <p><strong>Верен:</strong> <?= $row['is_correct'] ? 'Да' : 'Не' ?></p>
            <?php if (!empty($row['comment'])): ?>
                <p><strong>Коментар:</strong><br><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
            <?php endif; ?>
            <hr>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>