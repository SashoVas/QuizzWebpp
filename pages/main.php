<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../services/auth_helpers.php';

    session_start();
    check_auth_get();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Система</title>
    <!--<link rel="stylesheet" href="../styles/styles.css">-->
</head>
<body>
    <h1>Начална страница</h1>

    <!-- TODO: display specific error messages, handling logic should be separate -->
    <?php if (isset($_GET['message']) && $_GET['message'] === 'error'): ?>
        <div>
            Възникна грешка. Моля, опитайте отново.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'success'): ?>
        <div>
            Действието е успешно завършено.
        </div>
    <?php endif; ?>
    <!-- ^^^-->

    <h2>Качи CSV файл за тест</h2>
    <form action="../services/upload.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="file" name="csv">
        <input type="text" name="test_name" placeholder="Име на теста" required>
        <button type="submit">Качи</button>
    </form>

    <h2>Налични тестове</h2>
    <ul>
        <?php
        $stmt = $pdo->query("SELECT * FROM tests");
        foreach ($stmt as $row) {
            echo "<li>{$row['name']} 
                    <a href='test.php?id={$row['id']}'>Направи теста</a> 
                    <a href='review.php?id={$row['id']}'>Рецензия</a> 
                    <a href='view_reviews.php?id={$row['id']}'>Рецензии</a>
                  </li>";
        }
        ?>
    </ul>
</body>
</html>
