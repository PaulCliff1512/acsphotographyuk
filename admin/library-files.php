<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();

$imagesRoot = realpath(__DIR__ . '/../images');
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
$filesPerPage = 24;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$message = '';
$error = '';

function relativeImagePath(string $path, string $root): string
{
    return ltrim(str_replace('\\', '/', substr($path, strlen($root))), '/');
}

function safeImagePath(string $relativePath, string $root): string
{
    $target = realpath($root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));

    if ($target === false || strpos($target, $root) !== 0 || !is_file($target)) {
        return '';
    }

    return $target;
}

function displayImagePath(string $relativePath): string
{
    if (strpos($relativePath, '/') === false) {
        return 'images/' . $relativePath;
    }

    return $relativePath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $relativePath = $_POST['file'] ?? '';
    $targetPath = safeImagePath($relativePath, $imagesRoot);

    if ($targetPath === '') {
        $error = 'The selected image could not be found.';
    } elseif ($action === 'delete') {
        if (unlink($targetPath)) {
            $message = 'Image deleted successfully.';
        } else {
            $error = 'The image could not be deleted.';
        }
    }
}

$confirmFile = $_GET['confirm'] ?? '';
$confirmPath = $confirmFile !== '' ? safeImagePath($confirmFile, $imagesRoot) : '';

$files = [];
if ($imagesRoot !== false) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($imagesRoot, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $extension = strtolower($file->getExtension());
        if (!in_array($extension, $allowedExtensions, true)) {
            continue;
        }

        $fullPath = $file->getPathname();
        $files[] = [
            'relative' => relativeImagePath($fullPath, $imagesRoot),
            'size' => $file->getSize(),
            'modified' => $file->getMTime(),
        ];
    }
}

usort($files, function (array $first, array $second): int {
    return strcasecmp($first['relative'], $second['relative']);
});

$totalFiles = count($files);
$totalPages = max(1, (int) ceil($totalFiles / $filesPerPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $filesPerPage;
$pagedFiles = array_slice($files, $offset, $filesPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Image Folder Files | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --danger: #8b1f1f;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 1180px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: Arial, Helvetica, sans-serif;
      color: var(--text-main);
      background: linear-gradient(135deg, #faf8f2 0%, #f1ece2 100%);
    }

    img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .page {
      width: min(100% - 36px, var(--content-width));
      margin: 0 auto;
      padding: 34px 0 54px;
    }

    .top-links {
      display: flex;
      flex-wrap: wrap;
      gap: 16px;
      margin-bottom: 28px;
    }

    .top-links a {
      color: var(--text-soft);
      font-weight: 700;
      text-decoration: none;
    }

    .top-links a:hover,
    .top-links a:focus {
      color: var(--accent-dark);
    }

    h1,
    h2 {
      font-family: Georgia, "Times New Roman", serif;
      font-weight: 500;
      letter-spacing: 0;
    }

    h1 {
      margin: 0 0 12px;
      font-size: clamp(2.4rem, 7vw, 4.8rem);
      line-height: 1;
    }

    .intro {
      margin: 0 0 28px;
      color: var(--text-soft);
      font-size: 1.08rem;
      line-height: 1.6;
    }

    .notice,
    .confirm-panel,
    .empty {
      margin: 0 0 18px;
      padding: 16px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.7);
      font-weight: 700;
    }

    .notice--success {
      color: #27643a;
    }

    .notice--error {
      color: var(--danger);
    }

    .confirm-panel h2 {
      margin: 0 0 12px;
      font-size: 2rem;
      line-height: 1;
    }

    .confirm-layout {
      display: grid;
      grid-template-columns: 220px minmax(0, 1fr);
      gap: 18px;
      align-items: center;
    }

    .confirm-thumb {
      overflow: hidden;
      aspect-ratio: 4 / 3;
      border: 1px solid #000000;
      border-radius: var(--radius);
      background: var(--surface);
    }

    .confirm-panel p {
      margin: 0 0 14px;
      color: var(--text-soft);
      line-height: 1.5;
      word-break: break-word;
    }

    .file-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
    }

    .pagination {
      margin: 24px 0 0;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .pagination a,
    .pagination span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 44px;
      min-height: 44px;
      padding: 0 14px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: rgba(255, 255, 255, 0.7);
      font-weight: 700;
      text-decoration: none;
    }

    .pagination a:hover,
    .pagination a:focus,
    .pagination .current {
      border-color: var(--accent-dark);
      color: #ffffff;
      background: var(--accent-dark);
    }

    .file-card {
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.72);
    }

    .thumb {
      aspect-ratio: 4 / 3;
      background: var(--surface);
    }

    .file-content {
      padding: 14px;
    }

    .file-path {
      margin: 0 0 8px;
      color: var(--text-main);
      font-weight: 700;
      line-height: 1.45;
      word-break: break-word;
    }

    .file-meta {
      margin: 0 0 14px;
      color: var(--text-soft);
      font-size: 0.92rem;
      line-height: 1.45;
    }

    .button,
    button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      padding: 0 18px;
      border: 1px solid var(--accent-dark);
      border-radius: var(--radius);
      color: #ffffff;
      background: var(--accent-dark);
      font-size: 0.95rem;
      font-weight: 700;
      text-decoration: none;
      cursor: pointer;
    }

    .button--danger,
    button {
      border-color: var(--danger);
      background: var(--danger);
    }

    .button--plain {
      color: var(--accent-dark);
      background: transparent;
    }

    .confirm-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    @media (max-width: 920px) {
      .file-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 620px) {
      .file-grid {
        grid-template-columns: 1fr;
      }

      .confirm-layout {
        grid-template-columns: 1fr;
      }

      .button,
      button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="top-links">
      <a href="index.php">Admin home</a>
      <a href="upload.php">Upload image</a>
      <a href="images.php">Manage uploaded images</a>
    </div>

    <h1>Image Folder Files</h1>
    <p class="intro">This page lists images inside the original images folder and its subfolders. Delete only files you are sure are no longer required.</p>

    <?php if ($message !== ''): ?>
      <p class="notice notice--success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="notice notice--error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($confirmPath !== ''): ?>
      <section class="confirm-panel" aria-label="Confirm delete">
        <div class="confirm-layout">
          <div class="confirm-thumb">
            <img src="../images/<?php echo htmlspecialchars(str_replace('%2F', '/', rawurlencode($confirmFile))); ?>" alt="">
          </div>
          <div>
            <h2>Confirm Delete</h2>
            <p><?php echo htmlspecialchars($confirmFile); ?></p>
            <div class="confirm-actions">
              <form method="post">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="file" value="<?php echo htmlspecialchars($confirmFile); ?>">
                <button type="submit">Delete Image</button>
              </form>
              <a class="button button--plain" href="library-files.php">Cancel</a>
            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <?php if (count($files) === 0): ?>
      <p class="empty">No image files found.</p>
    <?php endif; ?>

    <section class="file-grid" aria-label="Image folder files">
      <?php foreach ($pagedFiles as $file): ?>
        <article class="file-card">
          <div class="thumb">
            <img src="../images/<?php echo htmlspecialchars(str_replace('%2F', '/', rawurlencode($file['relative']))); ?>" alt="">
          </div>
          <div class="file-content">
            <p class="file-path"><?php echo htmlspecialchars(displayImagePath($file['relative'])); ?></p>
            <p class="file-meta">
              <?php echo number_format($file['size'] / 1024, 1); ?> KB<br>
              Last changed <?php echo date('d M Y H:i', $file['modified']); ?>
            </p>
            <a class="button button--danger" href="library-files.php?confirm=<?php echo urlencode($file['relative']); ?>">Delete</a>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Image folder file pages">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?php echo $currentPage - 1; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($page = 1; $page <= $totalPages; $page++): ?>
          <?php if ($page === $currentPage): ?>
            <span class="current" aria-current="page"><?php echo $page; ?></span>
          <?php else: ?>
            <a href="?page=<?php echo $page; ?>"><?php echo $page; ?></a>
          <?php endif; ?>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?php echo $currentPage + 1; ?>">Next</a>
        <?php endif; ?>
      </nav>
    <?php endif; ?>
  </main>
</body>
</html>
