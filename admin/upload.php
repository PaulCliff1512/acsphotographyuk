<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();
require __DIR__ . '/../includes/db.php';

$message = '';
$error = '';
$allowedTypes = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
    'image/avif' => 'avif',
];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

function collectServerImages(string $folder, string $prefix, array $allowedExtensions): array
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
            'filename' => $file->getFilename(),
        ];
    }

    return $files;
}

$topicsStatement = $pdo->query('SELECT id, slug, name FROM topics WHERE is_active = 1 ORDER BY display_order, name');
$topics = $topicsStatement->fetchAll();
$serverImages = array_merge(
    collectServerImages(__DIR__ . '/../images', 'images', $allowedExtensions),
    collectServerImages(__DIR__ . '/../uploads', 'uploads', $allowedExtensions)
);

usort($serverImages, function (array $first, array $second): int {
    return strcasecmp($first['label'], $second['label']);
});

$serverImagesByPath = [];
foreach ($serverImages as $serverImage) {
    $serverImagesByPath[$serverImage['path']] = $serverImage;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicId = (int) ($_POST['topic_id'] ?? 0);
    $imageSource = $_POST['image_source'] ?? 'computer';
    $title = trim($_POST['title'] ?? '');
    $altText = trim($_POST['alt_text'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $displayOrder = max(0, (int) ($_POST['display_order'] ?? 0));
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    $selectedTopic = null;
    foreach ($topics as $topic) {
        if ((int) $topic['id'] === $topicId) {
            $selectedTopic = $topic;
            break;
        }
    }

    if (!$selectedTopic) {
        $error = 'Please choose a valid topic.';
    } elseif ($title === '') {
        $error = 'Please add an image title.';
    } elseif ($altText === '') {
        $error = 'Please add image alt text.';
    } else {
        $filename = '';
        $imagePath = '';
        $originalFilename = '';

        if ($imageSource === 'computer') {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Please choose an image to upload.';
            } else {
                $imageInfo = getimagesize($_FILES['image']['tmp_name']);
                $mimeType = $imageInfo['mime'] ?? '';

                if (!isset($allowedTypes[$mimeType])) {
                    $error = 'Only JPG, PNG, WEBP, GIF, and AVIF images are allowed.';
                } else {
                    $uploadDirectory = __DIR__ . '/../uploads/' . $selectedTopic['slug'];

                    if (!is_dir($uploadDirectory)) {
                        mkdir($uploadDirectory, 0755, true);
                    }

                    $extension = $allowedTypes[$mimeType];
                    $safeTitle = preg_replace('/[^a-z0-9]+/', '-', strtolower($title));
                    $safeTitle = trim($safeTitle, '-');
                    $filename = $safeTitle . '-' . time() . '.' . $extension;
                    $targetPath = $uploadDirectory . '/' . $filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $imagePath = 'uploads/' . $selectedTopic['slug'] . '/' . $filename;
                        $originalFilename = $_FILES['image']['name'];
                    } else {
                        $error = 'The image could not be saved.';
                    }
                }
            }
        } elseif ($imageSource === 'server') {
            $selectedServerPath = trim($_POST['server_image'] ?? '');

            if ($selectedServerPath === '' || !isset($serverImagesByPath[$selectedServerPath])) {
                $error = 'Please choose an existing server image.';
            } else {
                $serverImage = $serverImagesByPath[$selectedServerPath];
                $filename = $serverImage['filename'];
                $imagePath = $serverImage['path'];
                $originalFilename = $serverImage['filename'];
            }
        } else {
            $error = 'Please choose where the image is coming from.';
        }

        if ($error === '') {
            $insertStatement = $pdo->prepare(
                'INSERT INTO images
                (topic_id, filename, image_path, original_filename, title, alt_text, caption, display_order, is_featured)
                VALUES
                (:topic_id, :filename, :image_path, :original_filename, :title, :alt_text, :caption, :display_order, :is_featured)'
            );

            $insertStatement->execute([
                'topic_id' => $topicId,
                'filename' => $filename,
                'image_path' => $imagePath,
                'original_filename' => $originalFilename,
                'title' => $title,
                'alt_text' => $altText,
                'caption' => $caption,
                'display_order' => $displayOrder,
                'is_featured' => $isFeatured,
            ]);

            $message = $imageSource === 'server' ? 'Server image added to the topic successfully.' : 'Image uploaded successfully.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Image | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 760px;
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

    .top-link {
      display: inline-block;
      margin-bottom: 28px;
      color: var(--text-soft);
      font-weight: 700;
      text-decoration: none;
    }

    .top-link:hover,
    .top-link:focus {
      color: var(--accent-dark);
    }

    h1 {
      margin: 0 0 12px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.4rem, 7vw, 4.6rem);
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

    .notice {
      margin: 0 0 18px;
      padding: 14px 16px;
      border-radius: var(--radius);
      background: var(--surface);
      border: 1px solid var(--line);
      font-weight: 700;
    }

    .notice--success {
      color: #27643a;
    }

    .notice--error {
      color: #8b1f1f;
    }

    form {
      padding: 24px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.65);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
    }

    input,
    select,
    textarea {
      width: 100%;
      margin-bottom: 18px;
      padding: 12px 13px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: #ffffff;
      font: inherit;
    }

    textarea {
      min-height: 110px;
      resize: vertical;
    }

    .source-options {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px;
      margin-bottom: 18px;
    }

    .source-option {
      display: flex;
      align-items: center;
      gap: 10px;
      min-height: 54px;
      padding: 12px 14px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: #ffffff;
      font-weight: 700;
      cursor: pointer;
    }

    .source-option input {
      width: auto;
      margin: 0;
    }

    .image-source-panel {
      display: none;
      margin-bottom: 4px;
    }

    .image-source-panel.is-visible {
      display: block;
    }

    .checkbox-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 22px;
    }

    .checkbox-row input {
      width: auto;
      margin: 0;
    }

    .checkbox-row label {
      margin: 0;
    }

    .image-preview {
      display: none;
      margin: 0 0 18px;
    }

    .image-preview.is-visible {
      display: block;
    }

    .image-preview p {
      margin: 0 0 8px;
      color: var(--text-soft);
      font-weight: 700;
    }

    .image-preview-frame {
      overflow: hidden;
      max-width: 420px;
      aspect-ratio: 4 / 3;
      border: 1px solid #000000;
      border-radius: var(--radius);
      background: var(--surface);
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

    @media (max-width: 560px) {
      .source-options {
        grid-template-columns: 1fr;
      }

      form {
        padding: 18px;
      }

      button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <a class="top-link" href="../home.php">Back to site</a>
    <h1>Upload Image</h1>
    <p class="intro">Add images to the correct topic and choose their display order. Featured images can be used for moving preview panels.</p>

    <?php if ($message !== ''): ?>
      <p class="notice notice--success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="notice notice--error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label for="topic_id">Topic</label>
      <select id="topic_id" name="topic_id" required>
        <option value="">Choose a topic</option>
        <?php foreach ($topics as $topic): ?>
          <option value="<?php echo (int) $topic['id']; ?>"><?php echo htmlspecialchars($topic['name']); ?></option>
        <?php endforeach; ?>
      </select>

      <label>Image source</label>
      <div class="source-options">
        <label class="source-option" for="image_source_computer">
          <input id="image_source_computer" name="image_source" type="radio" value="computer" checked>
          Upload from computer
        </label>
        <label class="source-option" for="image_source_server">
          <input id="image_source_server" name="image_source" type="radio" value="server">
          Choose from server
        </label>
      </div>

      <div class="image-source-panel is-visible" data-source-panel="computer">
        <label for="image">Image</label>
        <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" required>

        <div class="image-preview" data-image-preview>
          <p>Selected image preview</p>
          <div class="image-preview-frame">
            <img src="" alt="" data-image-preview-img>
          </div>
        </div>
      </div>

      <div class="image-source-panel" data-source-panel="server">
        <label for="server_image">Server image</label>
        <select id="server_image" name="server_image" data-server-image-select>
          <option value="">Choose an image already on the server</option>
          <?php foreach ($serverImages as $serverImage): ?>
            <option value="<?php echo htmlspecialchars($serverImage['path']); ?>">
              <?php echo htmlspecialchars($serverImage['label']); ?>
            </option>
          <?php endforeach; ?>
        </select>

        <div class="image-preview" data-server-preview>
          <p>Server image preview</p>
          <div class="image-preview-frame">
            <img src="" alt="" data-server-preview-img>
          </div>
        </div>
      </div>

      <label for="title">Title</label>
      <input id="title" name="title" type="text" required>

      <label for="alt_text">Alt text</label>
      <input id="alt_text" name="alt_text" type="text" required>

      <label for="caption">Caption</label>
      <textarea id="caption" name="caption"></textarea>

      <label for="display_order">Display order</label>
      <input id="display_order" name="display_order" type="number" min="0" value="0">

      <div class="checkbox-row">
        <input id="is_featured" name="is_featured" type="checkbox" value="1">
        <label for="is_featured">Use as featured image</label>
      </div>

      <button type="submit">Upload Image</button>
    </form>
  </main>
  <script>
    const sourceInputs = document.querySelectorAll('input[name="image_source"]');
    const sourcePanels = document.querySelectorAll('[data-source-panel]');
    const imageInput = document.querySelector('#image');
    const imagePreview = document.querySelector('[data-image-preview]');
    const imagePreviewImg = document.querySelector('[data-image-preview-img]');
    const serverImageSelect = document.querySelector('[data-server-image-select]');
    const serverPreview = document.querySelector('[data-server-preview]');
    const serverPreviewImg = document.querySelector('[data-server-preview-img]');

    function updateImageSource() {
      const selectedSource = document.querySelector('input[name="image_source"]:checked').value;

      sourcePanels.forEach((panel) => {
        panel.classList.toggle('is-visible', panel.dataset.sourcePanel === selectedSource);
      });

      imageInput.required = selectedSource === 'computer';
      serverImageSelect.required = selectedSource === 'server';
    }

    imageInput.addEventListener('change', () => {
      const file = imageInput.files[0];

      if (!file || !file.type.startsWith('image/')) {
        imagePreview.classList.remove('is-visible');
        imagePreviewImg.src = '';
        imagePreviewImg.alt = '';
        return;
      }

      imagePreviewImg.src = URL.createObjectURL(file);
      imagePreviewImg.alt = file.name;
      imagePreview.classList.add('is-visible');
    });

    serverImageSelect.addEventListener('change', () => {
      const selectedPath = serverImageSelect.value;

      if (!selectedPath) {
        serverPreview.classList.remove('is-visible');
        serverPreviewImg.src = '';
        serverPreviewImg.alt = '';
        return;
      }

      serverPreviewImg.src = '../' + selectedPath;
      serverPreviewImg.alt = selectedPath;
      serverPreview.classList.add('is-visible');
    });

    sourceInputs.forEach((input) => {
      input.addEventListener('change', updateImageSource);
    });

    updateImageSource();
  </script>
</body>
</html>
