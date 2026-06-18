<?php
require __DIR__ . '/../includes/admin-auth.php';
requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Manual | ACS Photography UK</title>
  <style>
    :root {
      --page-bg: #f7f4ee;
      --surface: #ffffff;
      --text-main: #1f2528;
      --text-soft: #5d6468;
      --accent-dark: #6f4825;
      --line: rgba(31, 37, 40, 0.14);
      --radius: 8px;
      --content-width: 920px;
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

    h1,
    h2 {
      font-family: Georgia, "Times New Roman", serif;
      font-weight: 500;
      letter-spacing: 0;
    }

    h1 {
      margin: 0 0 12px;
      font-size: clamp(2.4rem, 7vw, 4.8rem);
      line-height: 1;
    }

    h2 {
      margin: 34px 0 12px;
      font-size: clamp(1.8rem, 4vw, 2.7rem);
      line-height: 1.05;
    }

    p,
    li {
      color: var(--text-soft);
      font-size: 1.04rem;
      line-height: 1.6;
    }

    .manual {
      padding: 26px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      background: rgba(255, 255, 255, 0.7);
    }

    code,
    pre {
      font-family: Consolas, Monaco, monospace;
      font-size: 0.95rem;
    }

    pre {
      overflow-x: auto;
      padding: 14px;
      border: 1px solid var(--line);
      border-radius: var(--radius);
      color: var(--text-main);
      background: var(--surface);
    }

    ol {
      padding-left: 22px;
    }

    @media (max-width: 560px) {
      .manual {
        padding: 18px;
      }
    }
  </style>
</head>
<body>
  <main class="page">
    <div class="top-links">
      <a href="index.php">Admin home</a>
      <a href="upload.php">Upload image</a>
      <a href="images.php">Manage images</a>
      <a href="../home.php">Back to site</a>
    </div>

    <article class="manual">
      <h1>Admin User Manual</h1>

      <h2>How To Open Admin</h2>
      <p>Go to the admin login page:</p>
      <pre>https://acsphotographyuk.co.uk/admin/login.php</pre>
      <p>Enter the admin password, then click Enter Admin.</p>

      <h2>Admin Password</h2>
      <p>The admin password is private and should only be given to people allowed to manage the site.</p>
      <p>If the password needs changing, ask the site owner or developer to update the admin password file.</p>

      <h2>Admin Home</h2>
      <p>After logging in, the admin home page shows the main choices:</p>
      <pre>Upload Image
Manage Images
Homepage Sliders
Image Folder Files
Manual</pre>

      <h2>Storage Usage</h2>
      <p>The admin home page shows how much image storage has been used.</p>
      <p>The storage limit is set to 1 GB.</p>
      <p>If storage reaches 75%, a warning appears. If storage reaches 90%, a red warning appears. If storage goes over the limit, the percentage continues above 100%.</p>
      <p>If the storage is over the limit for any length of time then the hosting company have the rights to remove some of the files </p>
	  <p>The user must keep image sizes under control.</p>

      <h2>Upload Image</h2>
      <ol>
        <li>Click Upload Image.</li>
        <li>Choose the correct topic: Landscapes or Creatures in Nature.</li>
        <li>Choose the image file. A preview of the selected image will appear before upload.</li>
        <li>Add a clear title.</li>
        <li>Add alt text describing the image.</li>
        <li>Add a caption if required.</li>
        <li>Set the display order. Lower numbers appear earlier.</li>
        <li>Tick Use as featured image if the image should appear in the homepage moving panels.</li>
        <li>Click Upload Image.</li>
      </ol>

      <h2>Manage Images</h2>
      <p>Use Manage Images to update images that have already been uploaded through admin.</p>
      <p>Click Edit beside an image to change:</p>
      <pre>Topic
Title
Alt text
Caption
Display order
Featured image
Active status</pre>
      <p>If Active status is unticked, the image will not show in the public gallery.</p>

      <h2>Homepage Sliders</h2>
      <p>Use Homepage Sliders to choose the images shown in the moving panels on the main site homepage.</p>
      <p>Each topic has three slots:</p>
      <pre>Landscapes Slot 1
Landscapes Slot 2
Landscapes Slot 3
Creatures in Nature Slot 1
Creatures in Nature Slot 2
Creatures in Nature Slot 3</pre>
      <p>Choose an image for each slot, then click Save Homepage Sliders.</p>
      <p>The choices can use images from the site image folders.</p>

      <h2>Image Folder File Manager</h2>
      <p>Use Image Folder Files to review images stored in the original images folder.</p>
      <p>This page includes images inside subfolders and shows:</p>
      <pre>Image preview
folder/filename
File size
Last changed date
Delete button</pre>
      <p>The page shows 24 images per page.</p>
      <p>When Delete is clicked, a confirmation screen appears with the image preview. The file is only deleted after clicking Delete Image on the confirmation screen.</p>
      <p>The delete function is a permanent DELETE! Only delete images that are no longer required.</p>

      <h2>Logout</h2>
      <p>Click Logout from the admin home page when finished.</p>
    </article>
  </main>
</body>
</html>
