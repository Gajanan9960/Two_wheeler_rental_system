# 🚀 Deployment Guide: InfinityFree / 000webhost

Since you are using **SQLite**, deployment is very straightforward. You don't need to configure a remote MySQL database. You just need to upload your files.

## 1. Prepare Your Files
We have already secured your `database/` and `logs/` folders with `.htaccess` files to prevent people from downloading your database.

**Verify these files exist on your computer:**
- `database/.htaccess`
- `logs/.htaccess`

## 2. Sign Up & Get FTP Details
1.  Go to [InfinityFree](https://www.infinityfree.com/) or [000webhost](https://www.000webhost.com/) and sign up.
2.  Create a "Account" or "Website".
3.  Find your **FTP Details** (usually in the Dashboard):
    *   **FTP Host** (e.g., `ftpupload.net`)
    *   **FTP Username** (e.g., `epiz_12345678`)
    *   **FTP Password** (your account password)
    *   **FTP Port** (usually `21`)

## 3. Upload Files
You can use a free FTP client like [FileZilla](https://filezilla-project.org/) or the online "File Manager" provided by the host.

### Using File Manager (Easiest)
1.  Open the **File Manager** from your hosting dashboard.
2.  Navigate to the `htdocs` (or `public_html`) folder.
    *   *Note: Delete any default `index.php` or `index.html` files currently there.*
3.  **Upload** all your project folders and files:
    *   `assets/`
    *   `config/`
    *   `database/` (Make sure your `.htaccess` and `ride_ease.db` are inside!)
    *   `includes/`
    *   `logs/`
    *   `pages/`
    *   `index.php`
    *   `.htaccess` (if you have one in root, otherwise creates rules for clean URLs if needed)

### ⚠️ IMPORTANT: Database Permissions
SQLite needs **Write Permissions** on the **Directory** it lives in.
1.  In File Manager, right-click the `database` folder.
2.  Select **Permissions** or **Chmod**.
3.  Set it to `777` (Read/Write/Execute for everyone).
    *   *Why?* So the web server can create lock files when writing to the DB.
    *   *Security:* The `.htaccess` file prevents web visitors from seeing it, but `777` allows the server to write to it.

## 4. Test Your Site
1.  Visit your new URL (e.g., `http://your-site.infinityfreeapp.com`).
2.  Try to **Log In** or **Book a Ride**.
3.  If you see "Database Error":
    *   Double-check the `database` folder permissions are `777`.
    *   Check `logs/php_error.log` (if you uploaded it) for details.

## Troubleshooting
- **Images not loading?**
    Ensure your paths in code are relative (e.g., `../assets/img/...`) and not absolute (server paths).
- **500 Internal Server Error?**
    It might be an issue with `.htaccess` configuration if the host restricts certain commands. Try removing `.htaccess` temporarily to isolate the issue.
