<?php require __DIR__ . '/../database/db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Система</title>
    <!--<link rel="stylesheet" href="../styles/styles.css">-->
</head>
<body>
    <h1>Начална страница</h1>

    <h2>Качи CSV файл за тест</h2>
    <form action="../services/upload.php" method="post" enctype="multipart/form-data">
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
