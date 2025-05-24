<?php
function visualize_message() {
    if (isset($_GET['message']) && $_GET['message'] === 'success') {
        echo '<div class="success-message">Операцията беше успешна!</div>';
    }
    if (isset($_GET['message']) && $_GET['message'] === 'registered') {
        echo '<div class="success-message">Регистрацията беше успешна! Моля, влезте с вашите данни.</div>';
    }
    if (isset($_GET['message']) && $_GET['message'] === 'error') {
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
            case 'no_results':
                echo '<div class="error-message">Няма намерени резултати за този тест.</div>';
                break;
            default:
                echo '<div class="error-message">Възникна грешка. Моля, опитайте отново.</div>';
        }
    }
}
?>