<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../services/logout.php';
    require __DIR__ . '/../helpers/message_visualizer.php';

    check_auth_get(['id']);
    validate_user_roles(['teacher', 'admin']);

    $test_id = intval($_GET['id']);

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Преглед на рецензии</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <?php 
    if ($reviews->rowCount() !== 0) {
        add_logout_button();
    }
    ?>
    <h2>Рецензии на тест</h2>
    <p><a href="main.php">← Начална страница</a></p>
    <?php 
        if ((!isset($_GET['error'])) && $reviews->rowCount() === 0) {
            header("Location: ./view_reviews.php?id={$test_id}&message=error&error=no_reviews");
        }
        visualize_message();
    ?>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <li>
                <strong>Ревю от:</strong> <?= htmlspecialchars($review['reviewer']) ?>
                <strong>За:</strong> <?= htmlspecialchars($review['user']) ?>
                <em>(<?= $review['review_time'] ?>)</em>
                &nbsp;|&nbsp;
                <a href="view_reviews.php?id=<?= $test_id ?>&review_id=<?= $review['id'] ?>">Прегледай</a>
            </li>
        <?php endforeach; ?>
    </ul>

<?php
if (!isset($_GET['review_id'])) {
    echo '</body></html>'; #browser should render page anyway, but add just in case
    exit;
}

$review_id = intval($_GET['review_id']);
$stmt = $pdo->prepare("
    SELECT q.question, d.is_correct, d.comment 
    FROM review_details d 
    JOIN questions q ON d.question_id = q.id 
    WHERE d.review_id = ?
");
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

</body>
</html>