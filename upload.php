
<?php
require 'config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin.php');
    exit;
}

verify_csrf();

$title = trim($_POST['title'] ?? '');
$type = $_POST['type'] ?? '';

if ($title === '' || !in_array($type, ['movie', 'music'], true)) {
    exit('Invalid title or media type.');
}

if (!isset($_FILES['media_file']) ||
    $_FILES['media_file']['error'] !== UPLOAD_ERR_OK) {
    exit('Upload failed. Check your PHP upload settings.');
}

$file = $_FILES['media_file'];

if ($file['size'] < 1 || $file['size'] > 500 * 1024 * 1024) {
    exit('Maximum file size is 500 MB.');
}

$extension = strtolower(
    pathinfo($file['name'], PATHINFO_EXTENSION)
);

$allowed = $type === 'movie'
    ? ['mp4', 'webm']
    : ['mp3', 'wav', 'ogg'];

if (!in_array($extension, $allowed, true)) {
    exit('Unsupported file format.');
}

$filename = bin2hex(random_bytes(16)) . '.' . $extension;

$directory = __DIR__ . '/media';

if (!is_dir($directory)) {
    mkdir($directory, 0755, true);
}

if (!move_uploaded_file(
    $file['tmp_name'],
    $directory . '/' . $filename
)) {
    exit('Could not save uploaded file.');
}

$stmt = $pdo->prepare(
    "INSERT INTO media (title, type, filename)
     VALUES (?, ?, ?)"
);

$stmt->execute([$title, $type, $filename]);

header('Location: admin.php');
exit;
?>
