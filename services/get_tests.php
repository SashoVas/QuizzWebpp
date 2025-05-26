<?php
require __DIR__ . '/../database/db.php';

function get_role_based_links($row) {
    $links = [
        'test' => "<a href='test.php?id={$row['id']}'>Направи теста</a>",
        'view_reviews' => "<a href='view_reviews.php?id={$row['id']}'>Виж съществуващи рецензии</a>",
        'review' => "<a href='review.php?id={$row['id']}'>Направи рецензия</a>",
        'export' => "<a href='../services/export_test.php?id={$row['id']}'>Експорт в XML</a>",
        'delete' => "
            <form action='../services/delete_test.php' method='post' onsubmit=\"return confirm('Сигурни ли сте?');\">
                <input type='hidden' name='id' value='{$row['id']}'>
                <input type='hidden' name='csrf_token' value='{$_SESSION['csrf_token']}'>
                <button type='submit' class='delete-link'>Изтрий</button>
            </form>
        "
    ];

    $showLinks = [];

    if (isset($_SESSION['roles'])) {
        if (in_array('student', $_SESSION['roles'])) {
            array_push($showLinks, $links['test'], $links['view_reviews']);
        }
        if (in_array('teacher', $_SESSION['roles'])) {
            array_push($showLinks, $links['view_reviews'], $links['review'], $links['export']);
        }
        else if (in_array('admin', $_SESSION['roles'])) {
            foreach ($links as $link) {
                array_push($showLinks, $link);
            }
        }
    }

    return $showLinks;
}

function get_roles() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM tests");
    foreach ($stmt as $row) {
        $links = get_role_based_links($row);
        echo "<li>
                <span class='test-name'>{$row['name']}</span>
                <span class='test-links'>" . implode('', $links) . "</span>
            </li>";
    }
}

?>