<?php
    require __DIR__ . '/../services/auth_helpers.php';
    
    session_start();
    generate_csrf_in_session();

    $errors = $_SESSION['form_errors'] ?? [];
    $inputs = $_SESSION['form_inputs'] ?? [];
    unset($_SESSION['form_errors'], $_SESSION['form_inputs']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
</head>
<body>
    <h1>Вход</h1>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'registered'): ?>
        <div>
            Регистрацията беше успешна! Моля, влезте с вашите данни.
        </div>
    <?php endif; ?>

    <form method="post" action="../services/validate_login.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="form-group">
            <label for="username">Потребителско име или имейл:</label>
            <input type="text" id="username" name="username"
                   value="<?= htmlspecialchars($inputs['username'] ?? '') ?>"
                   class="<?= isset($errors['username']) ? 'error-border' : '' ?>">
            <?php if (isset($errors['username'])): ?>
                <div class="error"><?= htmlspecialchars($errors['username']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="password">Парола:</label>
            <input type="password" id="password" name="password"
                   class="<?= isset($errors['password']) ? 'error-border' : '' ?>">
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit">Влез</button>
    </form>

    <?php if (isset($errors['final'])): ?>
        <div class="error"><?= htmlspecialchars($errors['final']) ?></div>
    <?php endif; ?>

    <p>Нямаш акаунт? <a href="register.php">Регистрирай се тук</a></p>
</body>
</html>
