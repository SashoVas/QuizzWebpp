<?php
require __DIR__ . '/../helpers/auth_helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_auth_post(['logout']);
    $_SESSION = array();
    session_destroy();

    # Clear the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    header("Location: ../pages/login.php");
}

function add_logout_button() {
    echo '
    <div class="test-links">
        <form method="post" action="../services/logout.php" id="logout-form">
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">
            <input type="hidden" name="logout" value="1">
            <button type="submit" class="logout-btn" id="specific-button">Изход</button>
        </form>
    </div>
    ';
}


?>