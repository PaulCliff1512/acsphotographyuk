<?php
require __DIR__ . '/includes/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contactMessage = trim($_POST['message'] ?? '');

    if ($name === '') {
        $error = 'Please add your name.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please add a valid email address.';
    } elseif ($contactMessage === '') {
        $error = 'Please add your message.';
    } else {
        $insertStatement = $pdo->prepare(
            'INSERT INTO contact_messages (name, email, message)
             VALUES (:name, :email, :message)'
        );

        $insertStatement->execute([
            'name' => $name,
            'email' => $email,
            'message' => $contactMessage,
        ]);

        $message = 'Thank you. Your message has been sent.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 900px;
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

    .site-header,
    .contact-page {
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

    .contact-page {
      padding: 34px 0 64px;
    }

    h1 {
      margin: 0 0 14px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(3rem, 7vw, 6rem);
      line-height: 1;
      font-weight: 500;
      letter-spacing: 0;
    }

    .intro {
      max-width: 680px;
      margin: 0 0 28px;
      color: var(--text-soft);
      font-size: clamp(1.05rem, 1.5vw, 1.25rem);
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
      min-height: 150px;
      resize: vertical;
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

    @media (max-width: 700px) {
      .site-header,
      .contact-page {
        width: min(100% - 36px, var(--content-width));
      }

      .site-header {
        align-items: flex-start;
        flex-direction: column;
      }
    }

    @media (max-width: 560px) {
      .site-nav {
        width: 100%;
        justify-content: space-between;
        gap: 12px;
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
  <header class="site-header">
    <a class="logo" href="home.php">acsphotographyuk.co.uk</a>
    <nav class="site-nav" aria-label="Main navigation">
      <a href="home.php">Home</a>
      <a href="landscapes.php">Landscapes</a>
      <a href="creatures.php">Creatures</a>
    </nav>
  </header>

  <main class="contact-page">
    <h1>Contact</h1>
    <p class="intro">For enquiries, prints, events, or image requests, send a message and I will get back to you.</p>

    <?php if ($message !== ''): ?>
      <p class="notice notice--success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
      <p class="notice notice--error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
      <label for="name">Name</label>
      <input id="name" name="name" type="text" required>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="message">Message</label>
      <textarea id="message" name="message" required></textarea>

      <button type="submit">Send Message</button>
    </form>
  </main>
</body>
</html>
