<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../services/logout.php';
    require __DIR__ . '/../helpers/message_visualizer.php';

    check_auth_get(['id']);

    $test_id = intval($_GET['id']);

    $reviews = $pdo->prepare(
        "SELECT r.id, r.user_id, r.review_time, student.username AS user, reviewer.username AS reviewer
        FROM reviews r
        JOIN results u ON r.result_id = u.id
        JOIN users reviewer ON r.user_id = reviewer.id
        JOIN users student ON u.user_id = student.id
        WHERE u.test_id = ?
        ORDER BY r.review_time DESC"
    );

    $reviews->execute([$test_id]);
    $review_id = intval($_GET['review_id'] ?? '') ;

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
    <?php if ((!isset($_GET['error'])) && $reviews->rowCount() === 0) {
            header("Location: ./view_reviews.php?id={$test_id}&message=error&error=no_reviews");
        }
        visualize_message();
    ?>
    <ul>
        <?php foreach ($reviews as $review): ?>
            <li <?php if (isset($review_id) && $review['id'] == $review_id) echo ' class="selected"'; ?>>
                <strong>Ревю от:</strong> <?= htmlspecialchars($review['reviewer']) ?>
                <strong>За:</strong> <?= htmlspecialchars($review['user']) ?>
                <em>(<?= $review['review_time'] ?>)</em>
                <?php if(!isset($review_id) || $review['id'] != $review_id){?>
                    <a href="view_reviews.php?id=<?= $test_id ?>&review_id=<?= $review['id'] ?>">Прегледай</a>
            </li>
        <?php } endforeach; ?>
    </ul>

<?php
if (!isset($_GET['review_id'])) {
    echo '</body></html>'; #browser should render page anyway, but add just in case
    exit;
}
$result_stmt = $pdo->prepare("SELECT result_id FROM reviews WHERE id = ?");
$result_stmt->execute([$review_id]);
$result_row = $result_stmt->fetch(PDO::FETCH_ASSOC);
$result_id_for_review = $result_row ? $result_row['result_id'] : null;

$stmt = $pdo->prepare("
    SELECT q.question, d.is_correct, d.comment, a.answer
    FROM review_details d
    JOIN questions q ON d.question_id = q.id
    LEFT JOIN answers a ON a.question_id = d.question_id AND a.result_id = ?
    WHERE d.review_id = ?
");
$stmt->execute([$result_id_for_review, $review_id]);
$details = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

    <h3>Детайли на рецензия</h3>
    <?php foreach ($details as $row): ?>
        <div <?php if ($row['is_correct']) echo ' class="review-detail-true"';else  echo ' class="review-detail-false"'?>>
            <p><strong>Въпрос:</strong> <?= htmlspecialchars($row['question']) ?></p>
            <p><strong>Отговор на студент:</strong> <?= htmlspecialchars($row['answer']) ?></p>
            <p><strong>Верен:</strong> <?= $row['is_correct'] ? 'Да' : 'Не' ?></p>
            <?php if (!empty($row['comment'])): ?>
                <p><strong>Коментар:</strong><br><?= nl2br(htmlspecialchars($row['comment'])) ?></p>
            <?php endif; ?>
            <hr>
        </div>
    <?php endforeach; ?>

</body>
</html>