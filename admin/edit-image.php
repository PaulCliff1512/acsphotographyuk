<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();
require __DIR__ . '/../includes/db.php';

$imageId = (int) ($_GET['id'] ?? 0);
$message = '';
$error = '';

$topicsStatement = $pdo->query('SELECT id, slug, name FROM topics WHERE is_active = 1 ORDER BY display_order, name');
$topics = $topicsStatement->fetchAll();

$imageStatement = $pdo->prepare(
    'SELECT images.*, topics.slug AS topic_slug
     FROM images
     INNER JOIN topics ON images.topic_id = topics.id
     WHERE images.id = :id'
);
$imageStatement->execute(['id' => $imageId]);
$image = $imageStatement->fetch();

if (!$image) {
    exit('Image not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicId = (int) ($_POST['topic_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $altText = trim($_POST['alt_text'] ?? '');
    $caption = trim($_POST['caption'] ?? '');
    $displayOrder = max(0, (int) ($_POST['display_order'] ?? 0));
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

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
        if ($topicId !== (int) $image['topic_id']) {
            $oldPath = __DIR__ . '/../uploads/' . $image['topic_slug'] . '/' . $image['filename'];
            $newDirectory = __DIR__ . '/../uploads/' . $selectedTopic['slug'];
            $newPath = $newDirectory . '/' . $image['filename'];

            if (!is_dir($newDirectory)) {
                mkdir($newDirectory, 0755, true);
            }

            if (is_file($oldPath)) {
                rename($oldPath, $newPath);
            }
        }

        $updateStatement = $pdo->prepare(
            'UPDATE images
             SET topic_id = :topic_id,
                 title = :title,
                 alt_text = :alt_text,
                 caption = :caption,
                 display_order = :display_order,
                 is_featured = :is_featured,
                 is_active = :is_active
             WHERE id = :id'
        );

        $updateStatement->execute([
            'topic_id' => $topicId,
            'title' => $title,
            'alt_text' => $altText,
            'caption' => $caption,
            'display_order' => $displayOrder,
            'is_featured' => $isFeatured,
            'is_active' => $isActive,
            'id' => $imageId,
        ]);

        $message = 'Image updated successfully.';

        $imageStatement->execute(['id' => $imageId]);
        $image = $imageStatement->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Image | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 820px;
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

    h1 {
      margin: 0 0 12px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.4rem, 7vw, 4.6rem);
      line-height: 1;
      font-weight: 500;
      letter-spacing: 0;
    }

    .preview {
      overflow: hidden;
      max-width: 420px;
      aspect-ratio: 4 / 3;
      margin-bottom: 24px;
      border-radius: var(--radius);
      background: var(--surface);
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

    .checkbox-row {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 16px;
    }

    .checkbox-row input {
      width: auto;
      margin: 0;
    }

    .checkbox-row label {
      margin: 0;
    }

    button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 50px;
      margin-top: 8px;
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
    <div class="top-links">
      <a href="images.php">Back to images</a>
      <a href="index.php">Admin home</a>
    </div>

    <h1>Edit Image</h1>

    <div class="preview">
      <img src="../uploads/<?php echo htmlspecialchars($image['topic_slug']); ?>/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
    </div>

    <?php if ($message !== ''): ?>
      <p class="notice notice--success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="notice notice--error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
      <label for="topic_id">Topic</label>
      <select id="topic_id" name="topic_id" required>
        <?php foreach ($topics as $topic): ?>
          <option value="<?php echo (int) $topic['id']; ?>" <?php echo (int) $topic['id'] === (int) $image['topic_id'] ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($topic['name']); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="title">Title</label>
      <input id="title" name="title" type="text" value="<?php echo htmlspecialchars($image['title']); ?>" required>

      <label for="alt_text">Alt text</label>
      <input id="alt_text" name="alt_text" type="text" value="<?php echo htmlspecialchars($image['alt_text']); ?>" required>

      <label for="caption">Caption</label>
      <textarea id="caption" name="caption"><?php echo htmlspecialchars($image['caption']); ?></textarea>

      <label for="display_order">Display order</label>
      <input id="display_order" name="display_order" type="number" min="0" value="<?php echo (int) $image['display_order']; ?>">

      <div class="checkbox-row">
        <input id="is_featured" name="is_featured" type="checkbox" value="1" <?php echo $image['is_featured'] ? 'checked' : ''; ?>>
        <label for="is_featured">Use as featured image</label>
      </div>

      <div class="checkbox-row">
        <input id="is_active" name="is_active" type="checkbox" value="1" <?php echo $image['is_active'] ? 'checked' : ''; ?>>
        <label for="is_active">Image is active</label>
      </div>

      <button type="submit">Save Changes</button>
    </form>
  </main>
</body>
</html>
