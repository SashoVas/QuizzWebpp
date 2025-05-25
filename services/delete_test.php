<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

check_auth_post(['id']);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("DELETE FROM tests WHERE id = ?");
    $stmt->execute([$_POST['id']]);
    $pdo->commit();
    header("Location: ../pages/main.php?message=success&success=delete");
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: ../pages/main.php?message=error&error=delete");
}
?>