<?php
function visualize_message() {
    if (isset($_GET['message']) && $_GET['message'] === 'success') {
        visualize_success();
    } elseif (isset($_GET['message']) && $_GET['message'] === 'error') {
        visualize_error();
    }
}
function visualize_success() {
    $operation = $_GET['success'] ?? 'unknown';
    switch ($operation) {
        case 'register':
            echo '<div class="success-message">Успешна регистрация!</div>';
            break;
        case 'login':
            echo '<div class="success-message">Влезнахте успешно!</div>';
            break;
        default:
            echo '<div class="success-message">Операцията беше успешна!</div>';
    }
}

function visualize_error() {
    $error = $_GET['error'] ?? 'unknown';
    switch ($error) {
        case 'upload':
            echo '<div class="error-message">Грешка при качване на файла. Моля, опитайте отново.</div>';
            break;
        case 'auth':
            echo '<div class="error-message">Не сте влезли в системата. Моля, влезте отново.</div>';
            break;
        case 'save':
            echo '<div class="error-message">Грешка при запазване на отговорите. Моля, опитайте отново.</div>';
            break;
        case 'review':
            echo '<div class="error-message">Грешка при запазване на рецензията. Моля, опитайте отново.</div>';
            break;
        case 'bad_request':
            echo '<div class="error-message">Неправилна заявка. Моля, опитайте отново.</div>';
            break;
        case 'no_tests':
            echo '<div class="error-message">Тестът все още не е попълван от никого.</div>';
            break;
        case 'no_reviews':
            echo '<div class="error-message">Тестът все още няма рецензии от никого.</div>';
            break;
        default:
            echo '<div class="error-message">Възникна грешка. Моля, опитайте отново.</div>';
    }
}
?>