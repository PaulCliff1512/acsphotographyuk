<?php
require __DIR__ . '/../includes/admin-auth.php';

$adminConfig = require __DIR__ . '/../config/admin.php';
$error = '';

if (adminIsLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (hash_equals($adminConfig['password'], $password)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }

    $error = 'Password incorrect.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 460px;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: grid;
      place-items: center;
      font-family: Arial, Helvetica, sans-serif;
      color: var(--text-main);
      background: linear-gradient(135deg, #faf8f2 0%, #f1ece2 100%);
    }

    .login {
      width: min(100% - 36px, var(--content-width));
    }

    h1 {
      margin: 0 0 12px;
      font-family: Georgia, "Times New Roman", serif;
      font-size: clamp(2.4rem, 8vw, 4rem);
      line-height: 1;
      font-weight: 500;
      letter-spacing: 0;
    }

    p {
      margin: 0 0 24px;
      color: var(--text-soft);
      line-height: 1.6;
    }

    form {
      padding: 24px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.68);
    }

    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 700;
    }

    input {
      width: 100%;
      margin-bottom: 18px;
      padding: 12px 13px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: #ffffff;
      font: inherit;
    }

    .error {
      margin: 0 0 18px;
      padding: 14px 16px;
      border-radius: var(--radius);
      color: #8b1f1f;
      background: var(--surface);
      border: 1px solid var(--line);
      font-weight: 700;
    }

    button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 100%;
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
  </style>
</head>
<body>
  <main class="login">
    <h1>Admin Login</h1>
    <p>Enter the admin password to manage images and uploads.</p>

    <?php if ($error !== ''): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button type="submit">Enter Admin</button>
    </form>
  </main>
</body>
</html>
