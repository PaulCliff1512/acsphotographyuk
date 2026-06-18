<?php
require __DIR__ . '/includes/db.php';

$topicStatement = $pdo->prepare('SELECT id, name, description FROM topics WHERE slug = :slug AND is_active = 1');
$topicStatement->execute(['slug' => 'landscapes']);
$topic = $topicStatement->fetch();

if (!$topic) {
    exit('Topic not found.');
}

$imagesPerPage = 9;
$currentPage = max(1, (int) ($_GET['page'] ?? 1));

$countStatement = $pdo->prepare(
    'SELECT COUNT(*)
     FROM images
     WHERE topic_id = :topic_id AND is_active = 1'
);
$countStatement->execute(['topic_id' => $topic['id']]);
$totalImages = (int) $countStatement->fetchColumn();
$totalPages = max(1, (int) ceil($totalImages / $imagesPerPage));
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $imagesPerPage;

$imagesStatement = $pdo->prepare(
    'SELECT filename, title, alt_text, caption
     FROM images
     WHERE topic_id = :topic_id AND is_active = 1
     ORDER BY display_order, created_at DESC'
     . ' LIMIT :limit OFFSET :offset'
);
$imagesStatement->bindValue('topic_id', $topic['id'], PDO::PARAM_INT);
$imagesStatement->bindValue('limit', $imagesPerPage, PDO::PARAM_INT);
$imagesStatement->bindValue('offset', $offset, PDO::PARAM_INT);
$imagesStatement->execute();
$images = $imagesStatement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($topic['name']); ?> | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.12);
      --shadow: 0 20px 45px rgba(31, 37, 40, 0.12);
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

    .site-header,
    .page-intro,
    .gallery,
    .pagination {
      width: min(100% - 56px, var(--content-width));
      margin: 0 auto;
    }

    .site-header {
      padding: 28px 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 22px;
    }

    .logo {
      font-size: 1.1rem;
      font-weight: 700;
      text-decoration: none;
    }

    .site-nav {
      display: flex;
      align-items: center;
      gap: 18px;
      color: var(--text-soft);
      font-size: 0.98rem;
      font-weight: 700;
    }

    .site-nav a {
      color: inherit;
      text-decoration: none;
    }

    .site-nav a:hover,
    .site-nav a:focus {
      color: var(--accent-dark);
    }

    .page-intro {
      padding: 34px 0 38px;
    }

    .page-intro h1 {
      margin: 0 0 14px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(3rem, 7vw, 6rem);
      line-height: 1;
      font-weight: 500;
      letter-spacing: 0;
    }

    .page-intro p {
      max-width: 720px;
      margin: 0;
      color: var(--text-soft);
      font-size: clamp(1.05rem, 1.5vw, 1.25rem);
      line-height: 1.6;
    }

    .gallery {
      padding: 0 0 64px;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 22px;
    }

    .photo {
      overflow: hidden;
      border-radius: var(--radius);
      background: var(--surface);
      box-shadow: var(--shadow);
    }

    .photo-image {
      aspect-ratio: 4 / 3;
      background: var(--surface);
      cursor: zoom-in;
    }

    .photo-content {
      padding: 16px;
    }

    .photo-content h2 {
      margin: 0 0 8px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.45rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .photo-content p {
      margin: 0;
      color: var(--text-soft);
      line-height: 1.5;
    }

    .empty {
      grid-column: 1 / -1;
      padding: 24px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-soft);
      background: rgba(255, 255, 255, 0.62);
      font-weight: 700;
    }

    .lightbox {
      position: fixed;
      inset: 0;
      z-index: 20;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 24px;
      background: rgba(15, 18, 20, 0.92);
      cursor: zoom-out;
    }

    .lightbox.is-open {
      display: flex;
    }

    .lightbox img {
      width: auto;
      max-width: 100%;
      height: auto;
      max-height: 100%;
      object-fit: contain;
      border-radius: var(--radius);
    }

    .pagination {
      padding: 0 0 64px;
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

    @media (max-width: 900px) {
      .site-header,
      .page-intro,
      .gallery,
      .pagination {
        width: min(100% - 36px, var(--content-width));
      }

      .site-header {
        align-items: flex-start;
        flex-direction: column;
      }

      .gallery {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 620px) {
      .site-nav {
        width: 100%;
        justify-content: space-between;
        gap: 12px;
      }

      .gallery {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <header class="site-header">
    <a class="logo" href="home.php">acsphotographyuk.co.uk</a>
    <nav class="site-nav" aria-label="Main navigation">
      <a href="home.php">Home</a>
      <a href="creatures.php">Creatures</a>
      <a href="contact.php">Contact</a>
    </nav>
  </header>

  <main>
    <section class="page-intro">
      <h1><?php echo htmlspecialchars($topic['name']); ?></h1>
      <p><?php echo htmlspecialchars($topic['description']); ?></p>
    </section>

    <section class="gallery" aria-label="Landscape gallery">
      <?php if (count($images) === 0): ?>
        <p class="empty">No landscape images have been uploaded yet.</p>
      <?php endif; ?>

      <?php foreach ($images as $image): ?>
        <article class="photo">
          <div class="photo-image">
            <img src="uploads/landscapes/<?php echo htmlspecialchars($image['filename']); ?>" alt="<?php echo htmlspecialchars($image['alt_text']); ?>" data-fullscreen-image>
          </div>
          <div class="photo-content">
            <h2><?php echo htmlspecialchars($image['title']); ?></h2>
            <?php if ($image['caption'] !== ''): ?>
              <p><?php echo htmlspecialchars($image['caption']); ?></p>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="Landscape gallery pages">
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

  <div class="lightbox" data-lightbox aria-hidden="true">
    <img src="" alt="" data-lightbox-image>
  </div>

  <script>
    const lightbox = document.querySelector('[data-lightbox]');
    const lightboxImage = document.querySelector('[data-lightbox-image]');

    document.querySelectorAll('[data-fullscreen-image]').forEach((image) => {
      image.addEventListener('click', () => {
        lightboxImage.src = image.src;
        lightboxImage.alt = image.alt;
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
      });
    });

    lightbox.addEventListener('click', () => {
      lightbox.classList.remove('is-open');
      lightbox.setAttribute('aria-hidden', 'true');
      lightboxImage.src = '';
      lightboxImage.alt = '';
    });
  </script>
</body>
</html>
