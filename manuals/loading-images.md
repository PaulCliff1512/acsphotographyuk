# Manual: Loading Images Into The Site

## 1. Open The Upload Page

Open:

```text
http://localhost/Aidy/admin/upload.php
```

## 2. Choose The Correct Topic

Choose one of the available topics:

```text
Landscapes
Creatures in Nature
```

## 3. Choose The Image File

Select the image from your computer.

Allowed image types:

```text
JPG
PNG
WEBP
GIF
```

## 4. Add The Image Title

Add a clear title for the image.

Example:

```text
Winter at the Lake
```

## 5. Add Alt Text

Alt text should briefly describe the image.

Example:

```text
A winter landscape beside a quiet lake
```

## 6. Add A Caption

The caption is optional.

Use it if you want extra text to appear with the image in the gallery.

## 7. Set The Display Order

Lower numbers appear earlier in the gallery.

Example:

```text
1
2
3
4
```

## 8. Choose Featured Images

Tick:

```text
Use as featured image
```

if you want the image to appear in the moving image panels on the main page.

The main page uses up to 3 featured images per topic.

## 9. Upload The Image

Click:

```text
Upload Image
```

## 10. Check The Gallery

Check the correct gallery page:

```text
http://localhost/Aidy/landscapes.php
http://localhost/Aidy/creatures.php
```

## Where Images Are Saved

Landscape images are saved into:

```text
C:\xampp\htdocs\Aidy\uploads\landscapes
```

Creature images are saved into:

```text
C:\xampp\htdocs\Aidy\uploads\creatures
```

## Where Image Details Are Saved

Image details are saved into the database:

```text
photography
```

Table:

```text
images
```

## If An Image Does Not Appear

Check:

```text
The image was uploaded to the correct topic.
The image has a title.
The image has alt text.
The image file type is allowed.
The image is active in the database.
```
