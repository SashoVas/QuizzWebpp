<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

check_auth_post(['username', 'email', 'password', 'password_confirm'], true);

$errors = [];

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];

if (empty($username)) {
    $errors['username'] = 'Потребителското име е задължително';
} elseif (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username)) {
    $errors['username'] = 'Потребителското име може да съдържа само букви, цифри и долна черта и трябва да е между 5 и 20 символа';
}

if (empty($email)) {
    $errors['email'] = 'Имейлът е задължителен';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Моля въведете валиден имейл адрес';
}

if (empty($password)) {
    $errors['password'] = 'Паролата е задължителна';
} elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,30}$/', $password)) {
    $errors['password'] = 'Паролата трябва да е между 8 и 30 символа и да съдържа поне една малка буква, една главна буква и една цифра';
}

if ($password !== $password_confirm) {
    $errors['password_confirm'] = 'Паролите не съвпадат';
}

#check if username or email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    $errors['final'] = 'Потребителското име или имейлът вече съществува';
}

#register user if no errors
if (empty($errors)) {
    try {
        $pdo->beginTransaction();

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password_hash]);

        unset($_SESSION['form_errors'], $_SESSION['form_inputs']);

        $pdo->commit();

        header('Location: ../pages/login.php?message=success&success=register');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $errors['final'] = 'Възникна грешка при регистрацията. Моля, опитайте отново.';
    }
}

$_SESSION['form_errors'] = $errors;

#keeping password breaks security
$_SESSION['form_inputs'] = [
    'username' => $username,
    'email' => $email,
];
header('Location: ../pages/register.php');
exit;

?>