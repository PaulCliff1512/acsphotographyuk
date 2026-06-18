<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();

$storageConfig = require __DIR__ . '/../config/storage.php';
$storageLimitBytes = ((int) $storageConfig['limit_mb']) * 1024 * 1024;
$storageRoots = [
    realpath(__DIR__ . '/../images'),
    realpath(__DIR__ . '/../uploads'),
];
$storageUsedBytes = 0;

foreach ($storageRoots as $storageRoot) {
    if ($storageRoot === false || !is_dir($storageRoot)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($storageRoot, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $storageUsedBytes += $file->getSize();
        }
    }
}

$storagePercent = $storageLimitBytes > 0 ? ($storageUsedBytes / $storageLimitBytes) * 100 : 0;
$storageBarPercent = min(100, $storagePercent);
$storageUsedMb = $storageUsedBytes / 1024 / 1024;
$storageLimitMb = $storageLimitBytes / 1024 / 1024;
$storageLimitGb = $storageLimitMb / 1024;
$storageStatus = 'safe';

if ($storagePercent >= 90) {
    $storageStatus = 'danger';
} elseif ($storagePercent >= 75) {
    $storageStatus = 'warning';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --warning: #9a6a00;
      --danger: #9f1d1d;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 860px;
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
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      padding: 0 18px;
      border: 1px solid var(--accent-dark);
      border-radius: var(--radius);
      color: #ffffff;
      background: var(--accent-dark);
      font-weight: 700;
      text-decoration: none;
    }

    .top-links a:hover,
    .top-links a:focus {
      color: var(--accent-dark);
      background: transparent;
    }

    h1 {
      margin: 0 0 12px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.4rem, 7vw, 4.8rem);
      line-height: 1;
      font-weight: 500;
      letter-spacing: 0;
    }

    .intro {
      margin: 0 0 28px;
      color: var(--text-soft);
      font-size: 1.08rem;
      line-height: 1.6;
    }

    .admin-actions {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
    }

    .storage-panel {
      margin: 0 0 26px;
      padding: 22px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.72);
    }

    .storage-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 14px;
    }

    .storage-header h2 {
      margin: 0;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.8rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .storage-total {
      margin: 0;
      color: var(--text-soft);
      font-weight: 700;
      text-align: right;
    }

    .storage-track {
      overflow: hidden;
      height: 18px;
      border: 1px solid var(--line);
      border-radius: 999px;
      background: #ffffff;
    }

    .storage-fill {
      width: var(--storage-percent);
      height: 100%;
      border-radius: 999px;
      background: #27643a;
    }

    .storage-panel--warning .storage-fill {
      background: var(--warning);
    }

    .storage-panel--danger .storage-fill {
      background: var(--danger);
    }

    .storage-warning {
      margin: 14px 0 0;
      padding: 12px 14px;
      border: 1px solid rgba(159, 29, 29, 0.36);
      border-radius: var(--radius);
      color: var(--danger);
      background: rgba(159, 29, 29, 0.08);
      font-weight: 800;
      line-height: 1.45;
    }

    .action {
      padding: 22px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.68);
      text-decoration: none;
    }

    .action h2 {
      margin: 0 0 8px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.7rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .action p {
      margin: 0;
      color: var(--text-soft);
      line-height: 1.5;
    }

    @media (max-width: 620px) {
      .storage-header {
        flex-direction: column;
      }

      .storage-total {
        text-align: left;
      }

      .admin-actions {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="top-links">
      <a href="logout.php">Logout</a>
    </div>

    <h1>Admin</h1>
    <p class="intro">Manage uploads and image details for the photography galleries.</p>

    <section class="storage-panel storage-panel--<?php echo htmlspecialchars($storageStatus); ?>" aria-label="Storage usage">
      <div class="storage-header">
        <h2>Storage Usage</h2>
        <p class="storage-total">
          <?php echo number_format($storageUsedMb, 1); ?> MB used of <?php echo number_format($storageLimitGb, 1); ?> GB
          <br>
          <?php echo number_format($storagePercent, 1); ?>%
        </p>
      </div>
      <div class="storage-track" aria-hidden="true">
        <div class="storage-fill" style="--storage-percent: <?php echo htmlspecialchars((string) $storageBarPercent); ?>%;"></div>
      </div>

      <?php if ($storageStatus === 'warning'): ?>
        <p class="storage-warning">Storage is getting close to the <?php echo htmlspecialchars(number_format($storageLimitGb, 1)); ?> GB limit. Be careful when uploading more images.</p>
      <?php elseif ($storageStatus === 'danger'): ?>
        <p class="storage-warning">Storage is very close to the <?php echo htmlspecialchars(number_format($storageLimitGb, 1)); ?> GB limit. Delete unused images before adding more.</p>
      <?php endif; ?>
    </section>

    <section class="admin-actions" aria-label="Admin actions">
      <a class="action" href="upload.php">
        <h2>Upload Image</h2>
        <p>Add new images to Landscapes or Creatures in Nature.</p>
      </a>

      <a class="action" href="images.php">
        <h2>Manage Images</h2>
        <p>Edit titles, alt text, captions, display order, and featured status.</p>
      </a>

      <a class="action" href="homepage-sliders.php">
        <h2>Homepage Sliders</h2>
        <p>Choose which uploaded images appear in the moving homepage panels.</p>
      </a>

      <a class="action" href="landing-page.php">
        <h2>Landing Page</h2>
        <p>Choose which saved landing page design is active on the front page.</p>
      </a>

      <a class="action" href="library-files.php">
        <h2>Image Folder Files</h2>
        <p>Review original image folder files and delete files that are no longer required.</p>
      </a>

      <a class="action" href="manual.php">
        <h2>Manual</h2>
        <p>Read the working and testing guide for uploads, galleries, and admin use.</p>
      </a>
    </section>
  </main>
</body>
</html>
