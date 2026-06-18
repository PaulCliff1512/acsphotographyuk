<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();
require __DIR__ . '/../includes/db.php';

$imagesStatement = $pdo->query(
    'SELECT images.id, images.filename, images.title, images.alt_text, images.display_order,
            images.is_featured, images.is_active, topics.name AS topic_name, topics.slug AS topic_slug
     FROM images
     INNER JOIN topics ON images.topic_id = topics.id
     ORDER BY topics.display_order, images.display_order, images.created_at DESC'
);
$images = $imagesStatement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Images | ACS Photography UK</title>
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

    .image-list {
      display: grid;
      gap: 14px;
    }

    .image-row {
      display: grid;
      grid-template-columns: 120px minmax(0, 1fr) auto;
      gap: 18px;
      align-items: center;
      padding: 14px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.68);
    }

    .thumb {
      overflow: hidden;
      aspect-ratio: 4 / 3;
      border-radius: var(--radius);
      background: var(--surface);
    }

    .image-info h2 {
      margin: 0 0 6px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.45rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .image-info p {
      margin: 0 0 5px;
      color: var(--text-soft);
      line-height: 1.45;
    }

    .meta {
      font-size: 0.92rem;
      font-weight: 700;
    }

    .button {
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
    }

    .empty {
      padding: 24px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-soft);
      background: rgba(255, 255, 255, 0.62);
      font-weight: 700;
    }

    @media (max-width: 720px) {
      .image-row {
        grid-template-columns: 1fr;
      }

      .button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="top-links">
      <a href="../home.php">Back to site</a>
      <a href="upload.php">Upload image</a>
    </div>

    <h1>Manage Images</h1>
    <p class="intro">Choose an uploaded image to edit its title, alt text, caption, order, featured status, or active status.</p>

    <section class="image-list" aria-label="Uploaded images">
      <?php if (count($images) === 0): ?>
        <p class="empty">No images have been uploaded yet.</p>
      <?php endif; ?>

      <?php foreach ($images as $image): ?>
        <article class="image-row">
          <div class="thumb">
            <img src="../uploads/<?php echo htmlspecialchars($image['topic_slug']); ?>/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>">
          </div>
          <div class="image-info">
            <h2><?php echo htmlspecialchars($image['title']); ?></h2>
            <p><?php echo htmlspecialchars($image['alt_text']); ?></p>
            <p class="meta">
              <?php echo htmlspecialchars($image['topic_name']); ?> |
              Order <?php echo (int) $image['display_order']; ?> |
              <?php echo $image['is_featured'] ? 'Featured' : 'Not featured'; ?> |
              <?php echo $image['is_active'] ? 'Active' : 'Inactive'; ?>
            </p>
          </div>
          <a class="button" href="edit-image.php?id=<?php echo (int) $image['id']; ?>">Edit</a>
        </article>
      <?php endforeach; ?>
    </section>
  </main>
</body>
</html>
