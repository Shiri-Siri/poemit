# PoemIT! Installation Guide for VS Code & XAMPP

## Step 1: Install XAMPP

### Windows:
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Choose the version with PHP 8.0 or higher
3. Run the installer (xampp-windows-x64-installer.exe)
4. Install to default location: `C:\xampp\`
5. Select components: Apache, MySQL, PHP, phpMyAdmin
6. Complete the installation

### Mac:
1. Download XAMPP for macOS from the same link
2. Open the .dmg file and drag XAMPP to Applications
3. Default location: `/Applications/XAMPP/`

### Linux:
1. Download XAMPP for Linux (.run file)
2. Open terminal and run:
   ```bash
   chmod +x xampp-linux-x64-installer.run
   sudo ./xampp-linux-x64-installer.run
   ```
3. Default location: `/opt/lampp/`

---

## Step 2: Download Project Files

1. In v0, click the **three dots (⋮)** in the top right corner of the code block
2. Select **"Download ZIP"**
3. Save the file (e.g., `poemit.zip`) to your Downloads folder
4. Extract the ZIP file to get the project folder

---

## Step 3: Move Project to XAMPP

### Windows:
1. Open File Explorer
2. Navigate to: `C:\xampp\htdocs\`
3. Copy the entire project folder into htdocs
4. Rename it to `poemit` (lowercase, no spaces)
5. Final path should be: `C:\xampp\htdocs\poemit\`

### Mac:
1. Open Finder
2. Navigate to: `/Applications/XAMPP/htdocs/`
3. Copy the project folder there
4. Rename to `poemit`
5. Final path: `/Applications/XAMPP/htdocs/poemit/`

### Linux:
```bash
sudo cp -r /path/to/extracted/folder /opt/lampp/htdocs/poemit
sudo chmod -R 755 /opt/lampp/htdocs/poemit
```

---

## Step 4: Create Required Folders

Create these folders inside your project if they don't exist:

### Windows (using Command Prompt):
```cmd
cd C:\xampp\htdocs\poemit
mkdir uploads
mkdir uploads\images
mkdir uploads\documents
```

### Mac/Linux (using Terminal):
```bash
cd /Applications/XAMPP/htdocs/poemit  # Mac
# or
cd /opt/lampp/htdocs/poemit  # Linux

mkdir -p uploads/images
mkdir -p uploads/documents
chmod -R 777 uploads
```

---

## Step 5: Start XAMPP Services

1. Open **XAMPP Control Panel**
   - Windows: Start menu → XAMPP Control Panel
   - Mac: Applications → XAMPP → manager-osx
   - Linux: `sudo /opt/lampp/lampp start`

2. Click **Start** for:
   - ✅ **Apache** (web server)
   - ✅ **MySQL** (database)

3. Wait until both show "Running" status (green highlight)

**Troubleshooting:**
- If Apache won't start (Port 80 busy):
  - Click "Config" → "Apache (httpd.conf)"
  - Change `Listen 80` to `Listen 8080`
  - Change `ServerName localhost:80` to `ServerName localhost:8080`
  - Restart Apache
  - Access site at: `http://localhost:8080/poemit/`

---

## Step 6: Set Up Database

1. Open your web browser
2. Go to: `http://localhost/phpmyadmin`
3. Click **"New"** in the left sidebar
4. Database name: `poemit`
5. Collation: `utf8mb4_unicode_ci`
6. Click **"Create"**

7. Click on the `poemit` database you just created
8. Click the **"Import"** tab at the top
9. Click **"Choose File"**
10. Navigate to: `C:\xampp\htdocs\poemit\database\schema.sql`
11. Click **"Import"** at the bottom
12. You should see: "Import has been successfully finished"

### Verify Database Setup:
- Click on `poemit` database
- You should see 4 tables: `users`, `poems`, `likes`, `comments`

---

## Step 7: Configure Database Connection

1. Open the project in VS Code (see Step 8 first)
2. Open file: `config/database.php`
3. Check these settings match your XAMPP:

```php
private $host = "localhost";
private $db_name = "poemit";
private $username = "root";
private $password = "";  // Empty for default XAMPP
```

**Note:** Default XAMPP MySQL has:
- Username: `root`
- Password: `` (empty/blank)

If you changed MySQL password in XAMPP, update it here.

---

## Step 8: Open Project in VS Code

1. Download and install **VS Code** from: https://code.visualstudio.com/
2. Open VS Code
3. Click **File → Open Folder**
4. Navigate to: `C:\xampp\htdocs\poemit\` (or Mac/Linux equivalent)
5. Click **"Select Folder"**

### Install Recommended Extensions:

1. Click the **Extensions** icon (□) in the left sidebar
2. Search and install:
   - **PHP Intelephense** (by Ben Mewburn) - PHP intelligence
   - **PHP Debug** (by Xdebug) - Debugging support
   - **MySQL** (by Jun Han) - Database management
   - **HTML CSS Support** - Better HTML/CSS editing
   - **JavaScript (ES6) code snippets** - JS shortcuts

---

## Step 9: Access Your Website

1. Make sure Apache and MySQL are running in XAMPP
2. Open your web browser
3. Go to: **`http://localhost/poemit/`**

You should see the PoemIT! homepage!

---

## Step 10: Create Your First Account

1. Click **"Sign Up"** in the navigation
2. Fill in the form:
   - Full Name: Your Name
   - Username: yourname (no spaces)
   - Email: your@email.com
   - Password: (minimum 6 characters)
3. Click **"Sign Up"**
4. You'll be redirected to login
5. Log in with your credentials
6. Start writing poems!

---

## Common URLs

- **Homepage:** `http://localhost/poemit/`
- **Login:** `http://localhost/poemit/login.php`
- **Sign Up:** `http://localhost/poemit/signup.php`
- **Write Poem:** `http://localhost/poemit/write.php`
- **phpMyAdmin:** `http://localhost/phpmyadmin`

---

## Testing the Features

### 1. Write a Text Poem:
   - Click "Write" in navigation
   - Select "Text" format
   - Enter title and content
   - Click "Publish Poem"

### 2. Upload Image Poem:
   - Click "Write"
   - Select "Image" format
   - Enter title
   - Choose an image file
   - Click "Publish Poem"

### 3. Like a Poem:
   - Go to homepage
   - Click ❤️ icon on any poem
   - It should turn red and increment count

### 4. Comment on a Poem:
   - Click on a poem to view details
   - Scroll to comments section
   - Type your comment
   - Click "Post Comment"

---

## Troubleshooting

### Problem: "Can't connect to database"
**Solution:**
- Check MySQL is running in XAMPP
- Verify database `poemit` exists in phpMyAdmin
- Check credentials in `config/database.php`

### Problem: "Permission denied" for uploads
**Solution (Windows):**
- Right-click `uploads` folder
- Properties → Security
- Edit → Add → Everyone → Full Control

**Solution (Mac/Linux):**
```bash
chmod -R 777 /path/to/poemit/uploads
```

### Problem: Images not displaying
**Solution:**
- Check images exist in: `uploads/images/` folder
- Check browser console (F12) for errors
- Verify file permissions on uploads folder

### Problem: "404 Not Found"
**Solution:**
- Verify folder is in htdocs: `C:\xampp\htdocs\poemit\`
- Check Apache is running
- URL should be: `http://localhost/poemit/` (not `file:///...`)

### Problem: CSS/JS not loading
**Solution:**
- Check files exist in `assets/css/` and `assets/js/`
- Clear browser cache (Ctrl+Shift+Delete)
- Check browser console (F12) for errors

---

## File Structure

```
poemit/
├── api/
│   ├── likes.php          # Like/unlike API
│   └── comments.php       # Comment API
├── assets/
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       └── app.js         # Main JavaScript
├── config/
│   ├── config.php         # Site configuration
│   └── database.php       # Database connection
├── database/
│   └── schema.sql         # Database structure
├── includes/
│   ├── header.php         # Header template
│   └── footer.php         # Footer template
├── uploads/
│   ├── images/            # Uploaded images
│   └── documents/         # Uploaded documents
├── index.php              # Homepage
├── login.php              # Login page
├── signup.php             # Registration page
├── logout.php             # Logout handler
├── write.php              # Create poem page
├── poem.php               # View poem page
├── profile.php            # User profile page
├── .htaccess              # URL rewriting
└── README.md              # Project info
```

---

## Development Tips in VS Code

### 1. Quick File Navigation:
- Press `Ctrl+P` (or `Cmd+P` on Mac)
- Type filename to quickly open it

### 2. Search Across All Files:
- Press `Ctrl+Shift+F` (or `Cmd+Shift+F`)
- Search for text across entire project

### 3. Format Code:
- Right-click in file
- Select "Format Document"
- Or press `Shift+Alt+F`

### 4. Multi-cursor Editing:
- Hold `Alt` and click to add cursors
- Edit multiple places at once

### 5. View Database in VS Code:
- Install "MySQL" extension
- Click database icon in sidebar
- Add connection: localhost, root, (no password)
- Browse tables directly in VS Code

---

## Making Changes

After editing any PHP/HTML/CSS/JS file in VS Code:

1. **Save the file** (Ctrl+S or Cmd+S)
2. **Refresh your browser** (F5 or Ctrl+R)
3. **Check for errors** in browser console (F12)

**No need to restart Apache** for most changes!

---

## Security Notes

⚠️ **This is a development setup!**

For production deployment:
1. Change MySQL password from blank
2. Update `config/database.php` with new password
3. Set proper file permissions (755 for folders, 644 for files)
4. Enable HTTPS
5. Add CSRF protection
6. Implement rate limiting
7. Sanitize all user inputs more thoroughly

---

## Next Steps

Now you're ready to:
- ✅ Create accounts and log in
- ✅ Write and publish poems
- ✅ Upload images and documents
- ✅ Like and comment on poems
- ✅ View user profiles
- ✅ Search for poems

Enjoy building your poetry community! 🎭✨
