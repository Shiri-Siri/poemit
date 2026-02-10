# PoemIT! - PHP Version

A poetry sharing platform built with vanilla PHP, HTML, CSS, and JavaScript for XAMPP.

## Installation Instructions

### Prerequisites
- XAMPP (or similar: Apache, MySQL, PHP 7.4+)

### Setup Steps

1. **Copy files to XAMPP**
   - Copy the entire project folder to `C:\xampp\htdocs\poemit` (Windows)
   - Or `/opt/lampp/htdocs/poemit` (Linux)

2. **Create the database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click "Import" tab
   - Select `database/schema.sql` file
   - Click "Go" to create the database and tables

3. **Configure the application**
   - The default database settings are already configured for XAMPP
   - If needed, edit `config/database.php` to change:
     - Database host (default: localhost)
     - Database name (default: poemit)
     - Username (default: root)
     - Password (default: empty)

4. **Set permissions** (Linux/Mac only)
   ```bash
   chmod 755 uploads/
   chmod 644 uploads/.htaccess
   ```

5. **Access the application**
   - Open browser and go to: http://localhost/poemit
   - Create an account and start sharing poetry!

## Features
- User registration and authentication
- Write poems in text, image, or document format
- Like and comment on poems
- User profiles with poem collections
- Search and discover poems
- Responsive design

## File Structure
```
poemit/
├── index.php              # Home page with poem feed
├── login.php              # Login page
├── signup.php             # Registration page
├── write.php              # Create new poem
├── poem.php               # View poem details
├── profile.php            # User profile
├── logout.php             # Logout handler
├── config/                # Configuration files
│   ├── database.php       # Database connection
│   └── config.php         # General settings
├── database/              # Database schema
│   └── schema.sql         # MySQL schema
├── api/                   # API endpoints
│   ├── poems.php          # Poem operations
│   ├── likes.php          # Like operations
│   └── comments.php       # Comment operations
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   └── js/
│       └── app.js         # Main JavaScript
├── includes/              # Reusable components
│   ├── header.php         # Header component
│   └── footer.php         # Footer component
└── uploads/               # User uploaded files
```

## Default Database Credentials
- Host: localhost
- Database: poemit
- Username: root
- Password: (empty)

## Troubleshooting
- **Can't connect to database**: Make sure MySQL is running in XAMPP control panel
- **Permission denied on uploads**: Check folder permissions (Linux/Mac)
- **404 errors**: Ensure mod_rewrite is enabled in Apache
