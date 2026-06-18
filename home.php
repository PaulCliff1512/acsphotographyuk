<?php
require __DIR__ . '/includes/db.php';

$topicsStatement = $pdo->query(
    'SELECT id, slug, name, description
     FROM topics
     WHERE is_active = 1
     ORDER BY display_order, name'
);
$topics = $topicsStatement->fetchAll();

$featuredImages = [];
foreach ($topics as $topic) {
    $imagesStatement = $pdo->prepare(
        'SELECT filename, title, alt_text
         FROM images
         WHERE topic_id = :topic_id AND is_featured = 1 AND is_active = 1
         ORDER BY display_order, created_at DESC
         LIMIT 3'
    );
    $imagesStatement->execute(['topic_id' => $topic['id']]);
    $featuredImages[$topic['slug']] = $imagesStatement->fetchAll();
}

$sliderImages = [];
$sliderStatement = $pdo->query(
    'SELECT topics.slug, homepage_slider_slots.slot_number,
            homepage_slider_slots.image_path
     FROM homepage_slider_slots
     INNER JOIN topics ON homepage_slider_slots.topic_id = topics.id
     WHERE topics.is_active = 1 AND homepage_slider_slots.image_path IS NOT NULL
     ORDER BY topics.display_order, homepage_slider_slots.slot_number'
);

foreach ($sliderStatement->fetchAll() as $sliderImage) {
    $sliderImages[$sliderImage['slug']][] = [
        'src' => $sliderImage['image_path'],
        'alt' => $sliderImage['slug'] . ' homepage slider image',
    ];
}

$fallbackImages = [
    'landscapes' => [
        ['src' => 'images/Peak%20District/Z72_5902-Enhanced-NR.jpg', 'alt' => 'Peak District landscape photography'],
        ['src' => 'images/beach%20storm%20coming.jpg', 'alt' => 'Beach landscape with storm clouds'],
        ['src' => 'images/winter%20at%20the%20lake.jpg', 'alt' => 'Winter lake landscape photography'],
    ],
    'creatures' => [
        ['src' => 'images/SMJ%20Falconry/P5030294-Enhanced-NR-Edit.jpg', 'alt' => 'Owl portrait photography'],
        ['src' => 'images/Kingfishers%20Shropshire/Z72_3195-Enhanced-NR-Edit.jpg', 'alt' => 'Kingfisher nature photography'],
        ['src' => 'images/busy%20bee.jpg', 'alt' => 'Bee nature photography'],
    ],
];

function topicPageUrl(string $slug): string
{
    return $slug === 'creatures' ? 'creatures.php' : 'landscapes.php';
}

function imageSource(string $slug, array $image): string
{
    if (isset($image['src'])) {
        return $image['src'];
    }

    return 'uploads/' . $slug . '/' . rawurlencode($image['filename']);
}

function topicImages(string $slug, array $sliderImages, array $featuredImages, array $fallbackImages): array
{
    $images = $sliderImages[$slug] ?? [];
    if (count($images) === 0) {
        $images = $featuredImages[$slug] ?? [];
    }

    if (count($images) === 0) {
        $images = $fallbackImages[$slug] ?? [];
    }

    return array_slice($images, 0, 3);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ACS Photography UK | Home</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --paper: #fffdf8;
      --ink: #1f2528;
      --muted: #646a6d;
      --line: rgba(31, 37, 40, 0.14);
      --gold: #9a6a3a;
      --gold-dark: #6f4825;
      --forest: #263a31;
      --blue: #486777;
      --radius: 8px;
      --shadow: 0 26px 70px rgba(31, 37, 40, 0.16);
      --content: 1180px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      font-family: Arial, Helvetica, sans-serif;
      color: var(--ink);
      background: var(--page-bg);
    }

    img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    a {
      color: inherit;
    }

    .site-page {
      min-height: 100vh;
      background:
        linear-gradient(90deg, rgba(31, 37, 40, 0.06) 1px, transparent 1px) 0 0 / 92px 92px,
        linear-gradient(135deg, #fbf8f0 0%, #efe8dc 100%);
    }

    .site-header {
      width: min(100% - 56px, var(--content));
      margin: 0 auto;
      padding: 26px 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
    }

    .logo {
      font-size: 1.08rem;
      font-weight: 800;
      letter-spacing: 0;
      text-decoration: none;
    }

    .site-nav {
      display: flex;
      align-items: center;
      gap: 20px;
      color: var(--muted);
      font-size: 0.96rem;
      font-weight: 800;
    }

    .site-nav a {
      text-decoration: none;
    }

    .site-nav a:hover,
    .site-nav a:focus {
      color: var(--gold-dark);
    }

    .hero {
      width: min(100% - 56px, var(--content));
      min-height: calc(100vh - 92px);
      margin: 0 auto;
      display: grid;
      grid-template-columns: minmax(0, 0.9fr) minmax(360px, 1.1fr);
      gap: clamp(32px, 5vw, 76px);
      align-items: center;
      padding: 16px 0 58px;
    }

    .hero-copy {
      max-width: 620px;
    }

    .kicker {
      margin: 0 0 18px;
      color: var(--gold-dark);
      font-size: 0.8rem;
      font-weight: 900;
      letter-spacing: 0.16em;
      text-transform: uppercase;
    }

    .hero h1 {
      margin: 0 0 26px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(3.8rem, 8vw, 7.4rem);
      line-height: 0.9;
      font-weight: 500;
      letter-spacing: 0;
    }

    .hero-copy p {
      margin: 0 0 24px;
      color: var(--muted);
      font-size: clamp(1.08rem, 1.5vw, 1.28rem);
      line-height: 1.65;
    }

    .hero-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 50px;
      padding: 0 22px;
      border: 1px solid var(--gold-dark);
      border-radius: var(--radius);
      color: #ffffff;
      background: var(--gold-dark);
      font-size: 0.98rem;
      font-weight: 800;
      text-decoration: none;
      transition: background 180ms ease, color 180ms ease, transform 180ms ease;
    }

    .button:hover,
    .button:focus {
      color: var(--gold-dark);
      background: transparent;
      transform: translateY(-2px);
    }

    .button--secondary {
      color: var(--gold-dark);
      background: transparent;
    }

    .button--secondary:hover,
    .button--secondary:focus {
      color: #ffffff;
      background: var(--gold-dark);
    }

    .hero-gallery {
      display: grid;
      grid-template-columns: 0.78fr 1fr;
      gap: clamp(14px, 2vw, 22px);
      align-items: center;
    }

    .feature-frame {
      position: relative;
      overflow: hidden;
      border-radius: var(--radius);
      background: var(--paper);
      box-shadow: var(--shadow);
    }

    .feature-frame--tall {
      min-height: 520px;
      aspect-ratio: 4 / 5;
    }

    .feature-frame--wide {
      min-height: 330px;
      aspect-ratio: 5 / 4;
      margin-top: 82px;
    }

    .slide {
      position: absolute;
      inset: 0;
      opacity: 0;
      animation: fadeSlide 15s infinite;
    }

    .slide:nth-child(2) {
      animation-delay: 5s;
    }

    .slide:nth-child(3) {
      animation-delay: 10s;
    }

    .collection-band {
      padding: 76px 0;
      background: var(--paper);
      border-top: 1px solid var(--line);
      border-bottom: 1px solid var(--line);
    }

    .section-head,
    .collections,
    .footer-inner {
      width: min(100% - 56px, var(--content));
      margin: 0 auto;
    }

    .section-head {
      display: grid;
      grid-template-columns: minmax(0, 0.9fr) minmax(280px, 0.55fr);
      gap: clamp(24px, 5vw, 66px);
      align-items: end;
      margin-bottom: 34px;
    }

    .section-head h2 {
      margin: 0;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.7rem, 6vw, 5.8rem);
      line-height: 0.95;
      font-weight: 500;
      letter-spacing: 0;
    }

    .section-head p {
      margin: 0;
      color: var(--muted);
      font-size: 1.08rem;
      line-height: 1.62;
    }

    .collections {
      display: grid;
      gap: 26px;
    }

    .collection {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(300px, 0.44fr);
      min-height: 460px;
      overflow: hidden;
      border-radius: var(--radius);
      background: var(--forest);
      color: #ffffff;
      box-shadow: var(--shadow);
    }

    .collection:nth-child(even) {
      grid-template-columns: minmax(300px, 0.44fr) minmax(0, 1fr);
      background: var(--blue);
    }

    .collection:nth-child(even) .collection-slider {
      order: 2;
    }

    .collection-slider {
      position: relative;
      min-height: 460px;
      overflow: hidden;
    }

    .collection-content {
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: clamp(26px, 4vw, 42px);
    }

    .collection-number {
      margin-bottom: auto;
      color: rgba(255, 255, 255, 0.62);
      font-family: Georgia, "Times New Roman", serif;
      font-size: 3rem;
      line-height: 1;
    }

    .collection-content h3 {
      max-width: 100%;
      margin: 30px 0 14px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(1.9rem, 3vw, 3rem);
      line-height: 1.04;
      font-weight: 500;
      letter-spacing: 0;
      overflow-wrap: anywhere;
      text-wrap: balance;
    }

    .collection-content p {
      margin: 0 0 24px;
      color: rgba(255, 255, 255, 0.78);
      font-size: 1.05rem;
      line-height: 1.62;
    }

    .collection .button {
      width: fit-content;
      border-color: #ffffff;
      color: var(--ink);
      background: #ffffff;
    }

    .collection .button:hover,
    .collection .button:focus {
      color: #ffffff;
      background: transparent;
    }

    .quiet-panel {
      width: min(100% - 56px, var(--content));
      margin: 0 auto;
      padding: 70px 0;
      display: grid;
      grid-template-columns: minmax(0, 0.7fr) minmax(280px, 0.3fr);
      gap: clamp(24px, 5vw, 60px);
      align-items: center;
    }

    .quiet-panel h2 {
      margin: 0;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.4rem, 5vw, 4.8rem);
      line-height: 0.98;
      font-weight: 500;
      letter-spacing: 0;
    }

    .quiet-panel p {
      margin: 0 0 22px;
      color: var(--muted);
      font-size: 1.08rem;
      line-height: 1.62;
    }

    .site-footer {
      border-top: 1px solid var(--line);
      background: rgba(255, 255, 255, 0.54);
    }

    .footer-inner {
      padding: 24px 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
      color: var(--muted);
      font-size: 0.95rem;
      font-weight: 700;
    }

    .social-links {
      display: flex;
      flex-wrap: wrap;
      gap: 14px;
    }

    .social-links a {
      text-decoration: none;
    }

    .social-links a:hover,
    .social-links a:focus {
      color: var(--gold-dark);
    }

    @keyframes fadeSlide {
      0% {
        opacity: 0;
        transform: scale(1.08);
      }

      8%,
      30% {
        opacity: 1;
      }

      38%,
      100% {
        opacity: 0;
        transform: scale(1);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .slide {
        animation: none;
      }

      .slide:first-child {
        opacity: 1;
      }

      .button {
        transition: none;
      }
    }

    @media (max-width: 920px) {
      .site-header,
      .hero,
      .section-head,
      .collections,
      .quiet-panel,
      .footer-inner {
        width: min(100% - 36px, var(--content));
      }

      .hero,
      .section-head,
      .quiet-panel {
        grid-template-columns: 1fr;
      }

      .hero {
        min-height: auto;
      }

      .hero-gallery {
        max-width: 720px;
      }

      .collection,
      .collection:nth-child(even) {
        grid-template-columns: 1fr;
      }

      .collection:nth-child(even) .collection-slider {
        order: 0;
      }
    }

    @media (max-width: 640px) {
      .site-header {
        align-items: flex-start;
        flex-direction: column;
      }

      .site-nav {
        width: 100%;
        justify-content: space-between;
        gap: 12px;
      }

      .hero h1 {
        font-size: clamp(3.2rem, 17vw, 4.8rem);
      }

      .hero-gallery {
        grid-template-columns: 1fr;
      }

      .feature-frame--tall,
      .feature-frame--wide {
        min-height: 280px;
        aspect-ratio: 16 / 10;
        margin-top: 0;
      }

      .collection,
      .collection-slider {
        min-height: 360px;
      }

      .collection .button,
      .button {
        width: 100%;
      }

      .footer-inner {
        align-items: flex-start;
        flex-direction: column;
      }
    }
  </style>
</head>
<body>
  <div class="site-page">
    <header class="site-header" aria-label="Site header">
      <a class="logo" href="home.php">acsphotographyuk.co.uk</a>
      <nav class="site-nav" aria-label="Main navigation">
        <a href="landscapes.php">Landscapes</a>
        <a href="creatures.php">Creatures</a>
        <a href="contact.php">Contact</a>
      </nav>
    </header>

    <main>
      <section class="hero" aria-label="ACS Photography UK home page">
        <div class="hero-copy">
          <p class="kicker">Landscapes / Creatures / Natural Detail</p>
          <h1>Images with atmosphere, patience, and place.</h1>
          <p>ACS Photography UK brings together quiet landscape stories and close natural encounters, shaped by light, timing, and the detail that makes a moment worth keeping.</p>
          <div class="hero-actions">
            <a class="button" href="landscapes.php">Explore Landscapes</a>
            <a class="button button--secondary" href="creatures.php">Explore Creatures</a>
          </div>
        </div>

        <div class="hero-gallery" aria-label="Featured photography preview">
          <?php
            $landscapePreview = topicImages('landscapes', $sliderImages, $featuredImages, $fallbackImages);
            $creaturePreview = topicImages('creatures', $sliderImages, $featuredImages, $fallbackImages);
          ?>
          <div class="feature-frame feature-frame--tall">
            <?php foreach ($landscapePreview as $image): ?>
              <div class="slide">
                <img src="<?php echo htmlspecialchars(imageSource('landscapes', $image)); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?? $image['alt']); ?>">
              </div>
            <?php endforeach; ?>
          </div>
          <div class="feature-frame feature-frame--wide">
            <?php foreach ($creaturePreview as $image): ?>
              <div class="slide">
                <img src="<?php echo htmlspecialchars(imageSource('creatures', $image)); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?? $image['alt']); ?>">
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section class="collection-band" aria-label="Photography collections">
        <div class="section-head">
          <h2>Choose a collection.</h2>
          <p>Each area is built from uploaded images, ordered through the admin area, and designed to keep the photography large, clear, and easy to browse.</p>
        </div>

        <div class="collections">
          <?php foreach ($topics as $index => $topic): ?>
            <?php $images = topicImages($topic['slug'], $sliderImages, $featuredImages, $fallbackImages); ?>
            <article class="collection">
              <div class="collection-slider" aria-label="<?php echo htmlspecialchars($topic['name']); ?> moving preview">
                <?php foreach ($images as $image): ?>
                  <div class="slide">
                    <img src="<?php echo htmlspecialchars(imageSource($topic['slug'], $image)); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?? $image['alt']); ?>">
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="collection-content">
                <span class="collection-number">0<?php echo $index + 1; ?></span>
                <h3><?php echo htmlspecialchars($topic['name']); ?></h3>
                <p><?php echo htmlspecialchars($topic['description']); ?></p>
                <a class="button" href="<?php echo htmlspecialchars(topicPageUrl($topic['slug'])); ?>">View Collection</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <section class="quiet-panel" aria-label="Contact introduction">
        <h2>Prints, events, or image enquiries.</h2>
        <div>
          <p>Use the contact page for questions about images, sessions, events, or print requests.</p>
          <a class="button" href="contact.php">Contact ACS Photography UK</a>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="footer-inner">
        <span>ACS Photography UK</span>
        <div class="social-links" aria-label="Social media links">
          <a href="#" aria-label="Facebook">Facebook</a>
          <a href="#" aria-label="Instagram">Instagram</a>
          <a href="#" aria-label="YouTube">YouTube</a>
        </div>
      </div>
    </footer>
  </div>
</body>
</html>
