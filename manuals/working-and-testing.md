# Updated Manual: Working And Testing The Site

## Admin Login

Open:

```text
http://localhost/Aidy/admin/login.php
```

Current admin password:

```text
change-this-password
```

Change it here:

```text
C:\xampp\htdocs\Aidy\config\admin.php
```

## Main Pages To Test

Landing page:

```text
http://localhost/Aidy/index.html
```

The `Enter Site` button opens:

```text
http://localhost/Aidy/home.php
```

Main site:

```text
http://localhost/Aidy/home.php
```

Landscapes gallery:

```text
http://localhost/Aidy/landscapes.php
```

Creatures gallery:

```text
http://localhost/Aidy/creatures.php
```

Contact page:

```text
http://localhost/Aidy/contact.php
```

## Uploading Images

1. Login to admin.
2. Open:

```text
http://localhost/Aidy/admin/upload.php
```

3. Choose the topic:

```text
Landscapes
Creatures in Nature
```

4. Choose the image.
5. Add the title.
6. Add the alt text.
7. Add an optional caption.
8. Set the display order.
9. Tick `Use as featured image` if it should appear in the moving homepage panels.
10. Click `Upload Image`.

## Editing Images

Open:

```text
http://localhost/Aidy/admin/images.php
```

Click `Edit` beside an image.

You can change:

```text
Topic
Title
Alt text
Caption
Display order
Featured image
Active status
```

## Storage Usage Bar

Open the admin dashboard:

```text
http://localhost/Aidy/admin/index.php
```

The dashboard shows a storage usage bar based on the site image folders.

Current display limit:

```text
1 GB
```

The storage bar checks:

```text
images
uploads
```

Warning levels:

```text
75% or more = warning
90% or more = red warning
Over 100% = percentage continues to show the real amount used
```

Example:

```text
130%
```

This helps users be careful with large image uploads.

## Image Folder File Manager

Open:

```text
http://localhost/Aidy/admin/library-files.php
```

This page lists images inside:

```text
C:\xampp\htdocs\Aidy\images
```

including subfolders.

The listing shows:

```text
Image preview
folder/filename
File size
Last changed date
Delete button
```

The page shows 24 images per page.

Pagination appears automatically when there are more than 24 files.

Deleting an image uses a confirmation step.

The confirmation screen shows:

```text
Image preview
folder/filename
Delete Image button
Cancel button
```

Only delete files that are no longer required.

## How To Test Uploads

After uploading a Landscape image, check:

```text
http://localhost/Aidy/landscapes.php
```

After uploading a Creature image, check:

```text
http://localhost/Aidy/creatures.php
```

If an image is marked as featured, check:

```text
http://localhost/Aidy/home.php
```

The homepage uses up to 3 featured images per topic.

## Full Screen Image Viewing

On the gallery pages, click any image to open it full screen:

```text
http://localhost/Aidy/landscapes.php
http://localhost/Aidy/creatures.php
```

Click the full screen image again to close it and return to the gallery listing.

## Gallery Pagination

The gallery pages show 9 images per page:

```text
http://localhost/Aidy/landscapes.php
http://localhost/Aidy/creatures.php
```

Pagination appears automatically when a topic has more than 9 active images.

Use the page numbers, `Previous`, and `Next` links to move through the gallery.

## Where Images Are Stored

Landscape images:

```text
C:\xampp\htdocs\Aidy\uploads\landscapes
```

Creature images:

```text
C:\xampp\htdocs\Aidy\uploads\creatures
```

## Database

Database name:

```text
photography
```

Main image table:

```text
images
```
