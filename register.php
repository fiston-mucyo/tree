
<?php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!preg_match('/^[a-zA-Z0-9_.-]{3,40}$/', $username)) {
        $error = 'Invalid username.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password_hash, role)
                 VALUES (?, ?, 'subscriber')"
            );

            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            header('Location: login.php');
            exit;

        } catch (PDOException $e) {
            $error = 'Username already exists.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - StreamVerse</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth">

<form method="POST" class="box">
    <h1>Create Account</h1>

    <p class="error"><?= e($error) ?></p>

    <input type="hidden" name="csrf_token"
           value="<?= e(csrf_token()) ?>">

    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Register</button>

    <p>Already registered?
        <a href="login.php">Login</a>
    </p>
</form>

</body>
</html>
