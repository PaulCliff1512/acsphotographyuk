<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();

$message = '';
$error = '';
$landingPages = [
    'landing1.html' => 'Landing 1',
    'landing2.html' => 'Landing 2',
    'landing3.html' => 'Landing 3',
    'landing4.html' => 'Landing 4',
    'landing5.html' => 'Landing 5',
    'landing6.html' => 'Landing 6',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedLanding = $_POST['landing_page'] ?? '';

    if (!isset($landingPages[$selectedLanding])) {
        $error = 'Please choose a valid landing page.';
    } else {
        $sourcePath = realpath(__DIR__ . '/../' . $selectedLanding);
        $targetPath = __DIR__ . '/../index.html';

        if ($sourcePath === false || !is_file($sourcePath)) {
            $error = 'The selected landing page file could not be found.';
        } elseif (!is_writable($targetPath)) {
            $error = 'index.html is not writable. Please check file permissions.';
        } elseif (copy($sourcePath, $targetPath)) {
            $message = $landingPages[$selectedLanding] . ' is now the active landing page.';
        } else {
            $error = 'The landing page could not be applied.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Page | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
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

    .notice {
      margin: 0 0 18px;
      padding: 14px 16px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.72);
      font-weight: 700;
    }

    .notice--success {
      color: #27643a;
    }

    .notice--error {
      color: #8b1f1f;
    }

    form {
      padding: 22px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.68);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
    }

    select {
      width: 100%;
      margin-bottom: 18px;
      padding: 12px 13px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: #ffffff;
      font: inherit;
    }

    .preview-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
      margin-bottom: 18px;
    }

    .preview-panel h2 {
      margin: 0 0 10px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: 1.55rem;
      font-weight: 500;
      letter-spacing: 0;
    }

    .preview {
      overflow: hidden;
      height: 360px;
      border: 1px solid #000000;
      border-radius: var(--radius);
      background: var(--surface);
    }

    iframe {
      width: 100%;
      height: 100%;
      border: 0;
      background: #ffffff;
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

    @media (max-width: 620px) {
      .preview-grid {
        grid-template-columns: 1fr;
      }

      .preview {
        height: 280px;
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
      <a href="index.php">Admin home</a>
      <a href="../index.html">View landing page</a>
      <a href="../home.php">Back to site</a>
    </div>

    <h1>Landing Page</h1>
    <p class="intro">Choose one of the saved landing page designs. Applying a design copies it to index.html. The original landing files are kept unchanged.</p>

    <?php if ($message !== ''): ?>
      <p class="notice notice--success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="notice notice--error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
      <label for="landing_page">Landing page design</label>
      <select id="landing_page" name="landing_page" data-landing-select required>
        <?php foreach ($landingPages as $file => $label): ?>
          <option value="<?php echo htmlspecialchars($file); ?>"><?php echo htmlspecialchars($label); ?></option>
        <?php endforeach; ?>
      </select>

      <div class="preview-grid">
        <section class="preview-panel">
          <h2>Current index.html</h2>
          <div class="preview" aria-label="Current landing page preview">
            <iframe src="../index.html" title="Current active landing page"></iframe>
          </div>
        </section>

        <section class="preview-panel">
          <h2>Chosen replacement</h2>
          <div class="preview" aria-label="Chosen landing page preview">
            <iframe src="../landing1.html" title="Chosen landing page preview" data-landing-preview></iframe>
          </div>
        </section>
      </div>

      <button type="submit">Apply Landing Page</button>
    </form>
  </main>

  <script>
    const select = document.querySelector('[data-landing-select]');
    const preview = document.querySelector('[data-landing-preview]');

    select.addEventListener('change', () => {
      preview.src = '../' + select.value;
    });
  </script>
</body>
</html>
