<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();
require __DIR__ . '/../includes/db.php';

$message = '';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

function collectImageFiles(string $folder, string $prefix, array $allowedExtensions): array
{
    $root = realpath($folder);
    $files = [];

    if ($root === false || !is_dir($root)) {
        return $files;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        if (!in_array(strtolower($file->getExtension()), $allowedExtensions, true)) {
            continue;
        }

        $relative = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($root))), '/');
        $path = $prefix . '/' . $relative;

        $files[] = [
            'path' => $path,
            'label' => $path,
        ];
    }

    return $files;
}

$topicsStatement = $pdo->query(
    'SELECT id, slug, name
     FROM topics
     WHERE is_active = 1
     ORDER BY display_order, name'
);
$topics = $topicsStatement->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();

    foreach ($topics as $topic) {
        for ($slot = 1; $slot <= 3; $slot++) {
            $fieldName = 'slot_' . $topic['id'] . '_' . $slot;
            $imagePath = trim($_POST[$fieldName] ?? '');
            $imagePath = $imagePath !== '' ? $imagePath : null;

            $slotStatement = $pdo->prepare(
                'INSERT INTO homepage_slider_slots (topic_id, slot_number, image_id, image_path)
                 VALUES (:topic_id, :slot_number, NULL, :image_path)
                 ON DUPLICATE KEY UPDATE image_id = NULL, image_path = VALUES(image_path)'
            );
            $slotStatement->execute([
                'topic_id' => $topic['id'],
                'slot_number' => $slot,
                'image_path' => $imagePath,
            ]);
        }
    }

    $pdo->commit();
    $message = 'Homepage slider slots updated.';
}

$slotsStatement = $pdo->query(
    'SELECT topic_id, slot_number, image_path
     FROM homepage_slider_slots
     ORDER BY topic_id, slot_number'
);
$slots = [];
foreach ($slotsStatement->fetchAll() as $slot) {
    $slots[$slot['topic_id']][(int) $slot['slot_number']] = $slot['image_path'];
}

$imageFiles = array_merge(
    collectImageFiles(__DIR__ . '/../images', 'images', $allowedExtensions),
    collectImageFiles(__DIR__ . '/../uploads', 'uploads', $allowedExtensions)
);

usort($imageFiles, function (array $first, array $second): int {
    return strcasecmp($first['label'], $second['label']);
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Homepage Slider Images | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 1100px;
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
    h2,
    h3 {
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

    .notice {
      margin: 0 0 18px;
      padding: 14px 16px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: #27643a;
      background: rgba(255, 255, 255, 0.72);
      font-weight: 700;
    }

    .slider-columns {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 22px;
      align-items: start;
      margin-bottom: 24px;
    }

    .topic-column {
      padding: 18px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.62);
    }

    .topic-column h2 {
      margin: 0 0 16px;
      font-size: clamp(1.9rem, 4vw, 3rem);
      line-height: 1.05;
    }

    .slot-card {
      margin-bottom: 16px;
      padding: 14px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.78);
    }

    .slot-card h3 {
      margin: 0 0 10px;
      font-size: 1.35rem;
    }

    .slot-preview {
      overflow: hidden;
      aspect-ratio: 4 / 3;
      margin-bottom: 12px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: var(--surface);
    }

    .slot-empty {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      min-height: 160px;
      padding: 12px;
      color: var(--text-soft);
      font-weight: 700;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 8px;
      color: var(--text-soft);
      font-weight: 800;
    }

    select {
      width: 100%;
      padding: 12px 13px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: #ffffff;
      font: inherit;
    }

    button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 50px;
      padding: 0 22px;
      border: 1px solid var(--accent-dark);
      border-radius: var(--radius);
      color: #ffffff;
      background: var(--accent-dark);
      font-size: 0.98rem;
      font-weight: 700;
      cursor: pointer;
    }

    @media (max-width: 920px) {
      .slider-columns {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 620px) {
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
      <a href="images.php">Manage images</a>
      <a href="../home.php">Back to site</a>
    </div>

    <h1>Homepage Slider Images</h1>
    <p class="intro">Choose the exact image for each homepage slider slot. Each topic has three slots.</p>

    <?php if ($message !== ''): ?>
      <p class="notice"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post">
      <div class="slider-columns">
        <?php foreach ($topics as $topic): ?>
          <section class="topic-column">
            <h2><?php echo htmlspecialchars($topic['name']); ?></h2>

            <?php for ($slot = 1; $slot <= 3; $slot++): ?>
              <?php
                $selectedPath = $slots[$topic['id']][$slot] ?? '';
                $fieldName = 'slot_' . $topic['id'] . '_' . $slot;
              ?>
              <div class="slot-card">
                <h3>Slot <?php echo $slot; ?></h3>
                <div class="slot-preview">
                  <?php if ($selectedPath !== ''): ?>
                    <img src="../<?php echo htmlspecialchars($selectedPath); ?>" alt="">
                  <?php else: ?>
                    <div class="slot-empty">No image selected</div>
                  <?php endif; ?>
                </div>

                <label for="<?php echo htmlspecialchars($fieldName); ?>">Choose image</label>
                <select id="<?php echo htmlspecialchars($fieldName); ?>" name="<?php echo htmlspecialchars($fieldName); ?>" data-preview-select>
                  <option value="">No image selected</option>
                  <?php foreach ($imageFiles as $imageFile): ?>
                    <option value="<?php echo htmlspecialchars($imageFile['path']); ?>" <?php echo $imageFile['path'] === $selectedPath ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($imageFile['label']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            <?php endfor; ?>
          </section>
        <?php endforeach; ?>
      </div>

      <button type="submit">Save Homepage Sliders</button>
    </form>
  </main>
  <script>
    document.querySelectorAll('[data-preview-select]').forEach((select) => {
      select.addEventListener('change', () => {
        const preview = select.closest('.slot-card').querySelector('.slot-preview');
        const selectedPath = select.value;

        if (!selectedPath) {
          preview.innerHTML = '<div class="slot-empty">No image selected</div>';
          return;
        }

        preview.innerHTML = '';
        const image = document.createElement('img');
        image.src = '../' + selectedPath;
        image.alt = '';
        preview.appendChild(image);
      });
    });
  </script>
</body>
</html>
