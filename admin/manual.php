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
      <a href="logout.php">Logout</a>
    </div>

    <article class="manual">
      <h1>Admin User Manual</h1>

      <h2>Opening Admin</h2>
      <p>Go directly to the admin login page:</p>
      <pre>https://acsphotographyuk.co.uk/admin/login.php</pre>
      <p>Enter the admin password, then click Enter Admin.</p>

      <h2>Password</h2>
      <p>The admin password should only be shared with people allowed to manage the website.</p>
      <p>To change the password, update the password hash inside:</p>
      <pre>config/admin.php</pre>
      <p>After changing the password, logout and login again to test it.</p>

      <h2>Admin Home</h2>
      <p>After logging in, Admin Home shows the main tools:</p>
      <pre>Upload Image
Manage Images
Homepage Sliders
Landing Page
Image Folder Files
Manual</pre>

      <h2>Storage Usage</h2>
      <p>The admin home page shows how much image storage has been used.</p>
      <p>The storage limit is set to 1 GB.</p>
      <p>If storage reaches 75%, a warning appears. If storage reaches 90%, a red warning appears. If storage goes over the limit, the percentage continues above 100%.</p>
      <p>When the warning appears, delete unused images before adding more. Keep image sizes sensible so the hosting account stays healthy.</p>

      <h2>Upload Image</h2>
      <p>Use Upload Image when adding a picture to Landscapes or Creatures in Nature.</p>
      <p>There are two ways to add an image:</p>
      <pre>Upload from computer
Choose from server</pre>
      <p>Upload from computer adds a new file from your device. Choose from server uses an image that is already inside the website image folders.</p>
      <ol>
        <li>Click Upload Image.</li>
        <li>Choose the correct topic: Landscapes or Creatures in Nature.</li>
        <li>Choose Upload from computer or Choose from server.</li>
        <li>Select the image. A preview will appear before saving.</li>
        <li>Add a clear title.</li>
        <li>Add alt text describing the image.</li>
        <li>Add a caption if required.</li>
        <li>Set the display order. Lower numbers appear earlier.</li>
        <li>Tick Use as featured image if the image should be available as a featured image.</li>
        <li>Click Upload Image.</li>
      </ol>
      <p>After saving, the image will be available on the public topic page once it is active.</p>

      <h2>Manage Images</h2>
      <p>Use Manage Images to update pictures that are already connected to a topic.</p>
      <p>Click Edit beside an image to change its details:</p>
      <pre>Topic
Title
Alt text
Caption
Display order
Featured image
Active status</pre>
      <p>Changing the topic moves the image to the other public gallery. Display order controls the order on the gallery page.</p>
      <p>If Active status is unticked, the image will not show in the public gallery.</p>

      <h2>Homepage Sliders</h2>
      <p>Use Homepage Sliders to choose the images shown in the moving panels on the site landing page.</p>
      <p>Each topic has three slots:</p>
      <pre>Landscapes Slot 1
Landscapes Slot 2
Landscapes Slot 3
Creatures in Nature Slot 1
Creatures in Nature Slot 2
Creatures in Nature Slot 3</pre>
      <p>Choose an image for each slot, then click Save Homepage Sliders.</p>
      <p>The choices can use images from the website image folders.</p>

      <h2>Landing Page</h2>
      <p>Use Landing Page to choose which saved landing design is active on the front of the site.</p>
      <p>The page shows two previews:</p>
<pre>Current index.html
Choose a replacement</pre>
      <p>Select a landing page design from the dropdown, check the preview, then click Apply Landing Page.</p>
      <p>Applying a design copies it to the public front page. The saved designs remain unchanged inside the admin area.</p>

      <h2>Image Folder File Manager</h2>
      <p>Use Image Folder Files to review images stored in the website image folders.</p>
      <p>This page includes images inside subfolders and shows:</p>
      <pre>Image preview
folder/filename
File size
Last changed date
Delete button</pre>
      <p>The page shows 24 images per page.</p>
      <p>When Delete is clicked, a confirmation screen appears with the image preview. The file is only deleted after clicking Delete Image on the confirmation screen.</p>
      <p>Deleting a file is permanent. Only delete images that are no longer required anywhere on the site.</p>

      <h2>Logout</h2>
      <p>Click Logout from the admin home page when finished.</p>
      <p>Logout clears the admin session and returns to the public site. Opening admin again will require the password.</p>
    </article>
  </main>
</body>
</html>
