
<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('Location: index.php');
        exit;

    } else {
        $error = 'Incorrect username or password.';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - StreamVerse</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth">

<form method="POST" class="box">
    <h1>StreamVerse Login</h1>

    <p class="error"><?= e($error) ?></p>

    <input type="hidden" name="csrf_token"
           value="<?= e(csrf_token()) ?>">

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>

    <p>Don't have an account?
        <a href="register.php">Register</a>
    </p>
</form>

</body>
</html>
