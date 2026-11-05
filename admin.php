
<?php
require 'config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $id = (int)($_POST['delete_id'] ?? 0);

    $stmt = $pdo->prepare("SELECT filename FROM media WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if ($item) {
        $path = __DIR__ . '/media/' . basename($item['filename']);

        if (is_file($path)) {
            unlink($path);
        }

        $delete = $pdo->prepare("DELETE FROM media WHERE id = ?");
        $delete->execute([$id]);
    }

    header('Location: admin.php');
    exit;
}

$items = $pdo->query("SELECT * FROM media ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>StreamVerse Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1>StreamVerse Admin Dashboard</h1>
    <a href="index.php">View Website</a>
    <a href="logout.php">Logout</a>
</header>

<main class="panel">

<h2>Upload Movies and Music</h2>

<form action="upload.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="csrf_token"
           value="<?= e(csrf_token()) ?>">

    <label>Media Title</label>
    <input type="text" name="title" required>

    <label>Media Type</label>
    <select name="type">
        <option value="movie">Movie / Video</option>
        <option value="music">Music / Audio</option>
    </select>

    <label>Select File</label>
    <input type="file" name="media_file"
           accept=".mp4,.webm,.mp3,.wav,.ogg" required>

    <button type="submit">Upload Media</button>
</form>

<h2>Manage Media</h2>

<?php foreach ($items as $item): ?>
    <div class="media-row">
        <strong><?= e($item['title']) ?></strong>
        <span><?= e($item['type']) ?></span>

        <form method="POST"
              onsubmit="return confirm('Delete this media?')">

            <input type="hidden" name="csrf_token"
                   value="<?= e(csrf_token()) ?>">

            <input type="hidden" name="delete_id"
                   value="<?= (int)$item['id'] ?>">

            <button class="danger">Delete</button>
        </form>
    </div>
<?php endforeach; ?>

</main>
</body>
</html>
