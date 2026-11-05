
<?php
require 'config.php';

$items = $pdo->query(
    "SELECT * FROM media ORDER BY id DESC"
)->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>StreamVerse</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>🍿 StreamVerse</h1>

    <nav>
        <?php if (logged_in()): ?>
            Welcome <?= e($_SESSION['username']) ?>

            <?php if (is_admin()): ?>
                <a href="admin.php">Admin Dashboard</a>
            <?php endif; ?>

            <a href="logout.php">Logout</a>

        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<main>
    <h2>Movies and Music</h2>

    <?php foreach ($items as $item): ?>

        <div class="media-card">

            <h3><?= e($item['title']) ?></h3>

            <?php if ($item['type'] === 'movie'): ?>

                <video controls width="100%"
                    src="media/<?= e($item['filename']) ?>">
                </video>

            <?php else: ?>

                <audio controls
                    src="media/<?= e($item['filename']) ?>">
                </audio>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>

    <?php if (!$items): ?>
        <p>No media uploaded yet.</p>
    <?php endif; ?>

</main>

</body>
</html>
