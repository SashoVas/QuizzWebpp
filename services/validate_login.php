<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../services/auth_helpers.php';

check_auth_post(['username', 'password'], true);

$usernameOrEmail = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];

if (empty($usernameOrEmail)) {
    $errors['username'] = 'Потребителското име или имейл е задължително';
}

if (empty($password)) {
    $errors['password'] = 'Паролата е задължителна';
}

if (empty($errors)) {
    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); #set new token after login
        unset($_SESSION['form_errors'], $_SESSION['form_inputs']);
        header('Location: ../pages/main.php?message=success');
        exit;
    } else {
        $errors['final'] = 'Невалидни данни за вход';
    }
}

$_SESSION['form_errors'] = $errors;
$_SESSION['form_inputs'] = ['username' => $usernameOrEmail];
header('Location: ../pages/login.php');
exit;
