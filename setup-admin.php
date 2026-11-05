
<?php
require 'config.php';

$count = $pdo->query(
    "SELECT COUNT(*) FROM users WHERE role='admin'"
)->fetchColumn();

if ($count > 0) {
    exit('An administrator already exists. Delete this file.');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 12) {
        $message = 'Use a password of at least 12 characters.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, password_hash, role)
             VALUES (?, ?, 'admin')"
        );

        $stmt->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT)
        ]);

        exit('Admin created! Delete setup-admin.php now.');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth">

<form method="POST" class="box">
    <h1>Create Administrator</h1>

    <p><?= e($message) ?></p>

    <input type="hidden" name="csrf_token"
           value="<?= e(csrf_token()) ?>">

    <label>Username</label>
    <input name="username" required>

    <label>Password (12 characters minimum)</label>
    <input type="password" name="password" required minlength="12">

    <button type="submit">Create Admin</button>
</form>

</body>
</html>
