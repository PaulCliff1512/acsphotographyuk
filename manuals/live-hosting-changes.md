# Manual: Files To Change Before Going Live

## 1. Database Connection

File:

```text
C:\xampp\htdocs\Aidy\config\database.php
```

Change these values to the database details given by the live hosting company:

```php
'host' => 'localhost',
'database' => 'photography',
'username' => 'root',
'password' => '',
```

On live hosting, the database name may not be `photography`.

Some hosts use a database name like:

```text
hostingaccount_photography
```

## 2. Admin Password

File:

```text
C:\xampp\htdocs\Aidy\config\admin.php
```

Change this before the site goes live:

```php
'password' => 'change-this-password',
```

Use a strong private password.

## 3. Base URL

File:

```text
C:\xampp\htdocs\Aidy\settings.js
```

Check this value:

```javascript
BASE_URL: "https://acsphotography.co.uk"
```

If the live domain changes, update it here.

## 4. Upload Folders

Folders:

```text
C:\xampp\htdocs\Aidy\uploads\landscapes
C:\xampp\htdocs\Aidy\uploads\creatures
```

On the live server, these folders must allow image uploads.

If uploads fail, check the folder permissions in the hosting control panel or file manager.

## 5. Database Import

Local database:

```text
photography
```

Schema file:

```text
C:\xampp\htdocs\Aidy\database\schema.sql
```

Export the local database from XAMPP phpMyAdmin, then import it into the live hosting database.

## 6. Site Protection File

File:

```text
C:\xampp\htdocs\Aidy\.htaccess
```

This file should be uploaded to the live site root.

It protects:

```text
config
includes
database
manuals
```

It also disables folder browsing and blocks direct access to sensitive file types.

## 7. Admin Login URL

Local admin:

```text
http://localhost/Aidy/admin/login.php
```

Live admin:

```text
https://acsphotography.co.uk/admin/login.php
```

## 8. Main Live URLs

Landing page:

```text
https://acsphotography.co.uk/index.html
```

Main site:

```text
https://acsphotography.co.uk/home.php
```

Landscapes:

```text
https://acsphotography.co.uk/landscapes.php
```

Creatures:

```text
https://acsphotography.co.uk/creatures.php
```

Contact:

```text
https://acsphotography.co.uk/contact.php
```

## 9. Files That Should Not Need Changing

These should usually stay the same:

```text
index.html
home.php
landscapes.php
creatures.php
contact.php
admin/login.php
admin/index.php
admin/upload.php
admin/images.php
admin/edit-image.php
admin/manual.php
includes/db.php
includes/admin-auth.php
```

Only change them if the site design or behaviour needs changing.
