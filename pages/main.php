<?php
    require __DIR__ . '/../database/db.php';
    require __DIR__ . '/../helpers/message_visualizer.php';
    require __DIR__ . '/../services/logout.php';

    check_auth_get();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Система</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <?php add_logout_button(); ?>

    <h1>Начална страница</h1>

    <?php visualize_message(); ?>

    <h2>Качи CSV файл за тест</h2>
    <form action="../services/upload.php" class="upload-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="text" name="test_name" placeholder="Име на теста" required>
        <input type="file" name="csv" accept=".csv,text/csv" required>
        <button type="submit">Качи</button>
    </form>

    <h2>Налични тестове</h2>
    <ul>
        <?php
        $stmt = $pdo->query("SELECT * FROM tests");
        foreach ($stmt as $row) {
            echo "<li>
                      <span class='test-name'>{$row['name']}</span>
                      <span class='test-links'> 
                          <a href='test.php?id={$row['id']}'>Направи теста</a> 
                          <a href='review.php?id={$row['id']}'>Направи рецензия</a> 
                          <a href='view_reviews.php?id={$row['id']}'>Виж съществуващи рецензии</a>
                          <a href='../services/export_test.php?id={$row['id']}'>Експорт в XML</a>
                          <form action='../services/delete_test.php' method='post' onsubmit=\"return confirm('Сигурни ли сте, че искате да изтриете този тест?');\">
                              <input type='hidden' name='id' value='{$row['id']}'>
                              <input type='hidden' name='csrf_token' value='{$_SESSION['csrf_token']}'>
                              <button type='submit' class='delete-link'>Изтрий</button>
                          </form>
                      </span>
                  </li>";
        }
        ?>
    </ul>
</body>
</html>
