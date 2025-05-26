<?php

function generate_csrf_in_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function check_auth_get($fields = []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
        header("Location: ../pages/login.php?message=error&error=auth");
        exit;
    }
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        header("Location: ../pages/login.php?message=error&error=auth");
        exit;
    }

    foreach ($fields as $field) {
        if (!isset($_GET[$field]) || empty($_GET[$field])) {
            header("Location: ../pages/main.php?message=error&error=bad_request");
            exit;
        }
    }
}

function check_auth_post($fields = [], $prelogin = false) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!$prelogin && (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']))) {
        header("Location: ../pages/login.php?message=error&error=auth");
        exit;
    }

    if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        header("Location: ../pages/login.php?message=error&error=auth");
        exit;
    }

    foreach ($fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            header("Location: ../pages/main.php?message=error&error=bad_request");
            exit;
        }
    }
}

function validate_user_roles($roles) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['roles']) || !is_array($_SESSION['roles']) || empty($_SESSION['roles'])) {
        header("Location: ../pages/login.php?message=error&error=auth");
        exit;
    }
    
    $userRoles = $_SESSION['roles'];

    $found = False;
    foreach ($roles as $role) {
        if (in_array($role, $userRoles)) {
            $found = True;
            break;
        }
    }

    if (!$found) {
        header("Location: ../pages/main.php?message=error&error=auth");
        exit;
    }
}

function check_user_roles($roles) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['roles']) || !is_array($_SESSION['roles']) || empty($_SESSION['roles'])) {
        return false;
    }
    
    $userRoles = $_SESSION['roles'];

    $found = False;
    foreach ($roles as $role) {
        if (in_array($role, $userRoles)) {
           return true;
        }
    }

    return false;
}
?>