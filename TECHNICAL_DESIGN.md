# PoemIT! - Technical Design Document

## Version 1.0 | February 2026

---

## 1. SYSTEM ARCHITECTURE

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                           │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Presentation (HTML5, CSS3, JavaScript)              │   │
│  │ ├─ Responsive UI Components                         │   │
│  │ ├─ Form Validation (Client-side)                    │   │
│  │ ├─ API Communication (AJAX/Fetch)                   │   │
│  │ └─ Dynamic Content Updates                          │   │
│  └──────────────────────────────────────────────────────┘   │
└────────────────────────────────────────────────────────────┬┘
                                                              │
                         HTTP/AJAX
                                                              │
┌─────────────────────────────────────────────────────────────┴┐
│                      SERVER LAYER (PHP)                      │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ Routing Layer                                        │    │
│  │ ├─ URL Dispatcher                                   │    │
│  │ ├─ Method Routing (GET/POST/DELETE/PATCH)          │    │
│  │ └─ Authentication Check                             │    │
│  └──────────────────────────────────────────────────────┘    │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ Business Logic Layer (Controllers)                  │    │
│  │ ├─ AuthController                                  │    │
│  │ ├─ PoemController                                  │    │
│  │ ├─ UserController                                  │    │
│  │ ├─ AdminController                                 │    │
│  │ └─ EngagementController                            │    │
│  └──────────────────────────────────────────────────────┘    │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ Service Layer                                       │    │
│  │ ├─ AuthService (Login, Signup, Password Reset)     │    │
│  │ ├─ PoemService (CRUD operations)                   │    │
│  │ ├─ SearchService (Full-text search)                │    │
│  │ ├─ FileService (Upload/validation)                 │    │
│  │ └─ ValidationService (Data validation)             │    │
│  └──────────────────────────────────────────────────────┘    │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ Data Access Layer (DAL)                             │    │
│  │ ├─ UserDAO                                          │    │
│  │ ├─ PoemDAO                                          │    │
│  │ ├─ LikeDAO                                          │    │
│  │ └─ CommentDAO                                       │    │
│  └──────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────┬─┘
                                                              │
                        SQL Queries
                                                              │
┌─────────────────────────────────────────────────────────────┴┐
│                    DATABASE LAYER                            │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ MySQL Database (poemit)                             │    │
│  │ ├─ Users Table                                      │    │
│  │ ├─ Poems Table                                      │    │
│  │ ├─ Likes Table                                      │    │
│  │ ├─ Comments Table                                   │    │
│  │ └─ Indexes & Views                                  │    │
│  └──────────────────────────────────────────────────────┘    │
└────────────────────────────────────────────────────────────┬─┘
                                                              │
                      File System Access
                                                              │
┌─────────────────────────────────────────────────────────────┴┐
│                    FILE STORAGE                              │
│  ┌──────────────────────────────────────────────────────┐    │
│  │ /uploads/ Directory                                 │    │
│  │ ├─ User Poems (images, documents)                   │    │
│  │ ├─ User Avatars                                     │    │
│  │ └─ Static Assets                                    │    │
│  └──────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
```

### 1.2 MVC Architecture Pattern

```
M (Model)                    V (View)                  C (Controller)
│                           │                        │
├─ User Model              ├─ index.php             ├─ Router
├─ Poem Model              ├─ login.php             ├─ Request Handler
├─ Like Model              ├─ signup.php            ├─ Business Logic
├─ Comment Model           ├─ poem.php              ├─ Response Builder
└─ Database Connection     ├─ profile.php           └─ API Endpoints
                           └─ admin.php
```

### 1.3 Component Interaction Diagram

```
USER REQUEST
    │
    ▼
┌──────────────────┐
│   index.php      │ (Router/Entry Point)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ config/config.php│ (Load Configuration)
└────────┬─────────┘
         │
         ▼
┌──────────────────────────────┐
│ Session Management           │
│ - Check if logged in         │
│ - Verify CSRF token (if POST)│
└────────┬─────────────────────┘
         │
         ▼
┌──────────────────────────────┐
│ Route Controller             │
│ - Determine action           │
│ - Call appropriate method    │
└────────┬─────────────────────┘
         │
    ┌────┴────────────────────────────┐
    │                                 │
    ▼                                 ▼
┌─────────────────┐         ┌──────────────────┐
│ Backend Logic   │         │ Frontend Render  │
│ (API handlers)  │         │ (HTML templates) │
│ ├─ Validation   │         │ ├─ Header        │
│ ├─ Business     │         │ ├─ Content       │
│ ├─ DB Query     │         │ ├─ Footer        │
│ └─ Response     │         │ └─ JavaScript    │
└────────┬────────┘         └────────┬─────────┘
         │                           │
         ▼                           ▼
    ┌─────────────────────────────────┐
    │  Return Response (JSON/HTML)    │
    │  Set HTTP Headers & Status Code │
    └────────┬────────────────────────┘
             │
             ▼
        BROWSER DISPLAY
```

---

## 2. DATABASE DESIGN

### 2.1 Entity-Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────┐
│                      USERS                          │
├─────────────────────────────────────────────────────┤
│ PK: id (INT, AUTO_INCREMENT)                        │
│ UK: username (VARCHAR 50)                           │
│ UK: email (VARCHAR 255)                             │
│ : password_hash (VARCHAR 255) - Bcrypt             │
│ : full_name (VARCHAR 100)                           │
│ : bio (TEXT)                                        │
│ : is_admin (BOOLEAN)                                │
│ : avatar_url (VARCHAR 255)                          │
│ : created_at (TIMESTAMP)                            │
│ : updated_at (TIMESTAMP)                            │
│ FK: Poems (1:M) - user_id                           │
│ FK: Likes (1:M) - user_id                           │
│ FK: Comments (1:M) - user_id                        │
└─────────────────────────────────────────────────────┘
              1 │                  │ M
                │ writes           │
                │                  │ by
                │ 1              M │
┌───────────────────────────────────────────────────────────────┐
│                        POEMS                                  │
├───────────────────────────────────────────────────────────────┤
│ PK: id (INT, AUTO_INCREMENT)                                  │
│ FK: user_id (INT) - REFERENCES users(id) ON DELETE CASCADE   │
│ : title (VARCHAR 255)                                         │
│ : content (TEXT)                                              │
│ : format (ENUM: 'text', 'image', 'document')                 │
│ : file_url (VARCHAR 500)                                      │
│ : tags (VARCHAR 500)                                          │
│ : status (ENUM: 'pending', 'approved', 'rejected')           │
│ : views_count (INT, DEFAULT 0)                               │
│ : created_at (TIMESTAMP)                                      │
│ : updated_at (TIMESTAMP)                                      │
│ IDX: idx_user_id                                              │
│ IDX: idx_created_at                                           │
│ IDX: idx_format                                               │
│ FULLTEXT: idx_search (title, content, tags)                  │
│ FK: Likes (1:M) - poem_id                                     │
│ FK: Comments (1:M) - poem_id                                  │
└───────────────────────────────────────────────────────────────┘
                1 │                   │ M
                  │ receives          │
                  │                   │ on
                  │ M                1│
        ┌─────────────────┐  ┌─────────────────┐
        │     LIKES       │  │   COMMENTS      │
        ├─────────────────┤  ├─────────────────┤
        │ PK: id          │  │ PK: id          │
        │ FK: user_id     │  │ FK: user_id     │
        │ FK: poem_id     │  │ FK: poem_id     │
        │ : created_at    │  │ : content       │
        │ UK: (user_id,   │  │ : created_at    │
        │     poem_id)    │  │ : updated_at    │
        │ IDX: idx_poem_id│  │ IDX: idx_poem_id│
        │ IDX: idx_user_id│  │ IDX: idx_user_id│
        └─────────────────┘  │ IDX: idx_created│
                             └─────────────────┘
        1 │                   │ M
          │ gives             │
          │                   │ by
          │ M                1│
        └─────────────────────┘
```

### 2.2 Database Schema Definition

```sql
-- Create database
CREATE DATABASE IF NOT EXISTS poemit 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE poemit;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    bio TEXT,
    is_admin BOOLEAN DEFAULT FALSE,
    avatar_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Poems Table
CREATE TABLE IF NOT EXISTS poems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    format ENUM('text', 'image', 'document') DEFAULT 'text',
    file_url VARCHAR(500),
    tags VARCHAR(500),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    views_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_format (format),
    INDEX idx_status (status),
    FULLTEXT INDEX idx_search (title, content, tags)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Likes Table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    poem_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (poem_id) REFERENCES poems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, poem_id),
    INDEX idx_poem_id (poem_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    poem_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (poem_id) REFERENCES poems(id) ON DELETE CASCADE,
    INDEX idx_poem_id (poem_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Poem Statistics View
CREATE OR REPLACE VIEW poem_stats AS
SELECT 
    p.id as poem_id,
    COUNT(DISTINCT l.id) as likes_count,
    COUNT(DISTINCT c.id) as comments_count
FROM poems p
LEFT JOIN likes l ON p.id = l.poem_id
LEFT JOIN comments c ON p.id = c.poem_id
GROUP BY p.id;
```

### 2.3 Indexing Strategy

| Table | Column | Type | Purpose |
|-------|--------|------|---------|
| users | username | UK | Unique constraint, login lookup |
| users | email | UK | Unique constraint, recovery |
| users | created_at | INDEX | User analytics |
| poems | user_id | INDEX/FK | Author lookups |
| poems | created_at | INDEX | Chronological ordering |
| poems | format | INDEX | Format filtering |
| poems | status | INDEX | Status filtering |
| poems | title, content, tags | FULLTEXT | Search optimization |
| likes | user_id | INDEX | User's likes |
| likes | poem_id | INDEX | Poem's likes |
| likes | (user_id, poem_id) | UNIQUE | Prevent duplicates |
| comments | user_id | INDEX | User's comments |
| comments | poem_id | INDEX | Poem's comments |
| comments | created_at | INDEX | Recent comments |

### 2.4 Query Performance Optimization

```php
// Example: Optimized query with joins and indexing
$query = "
    SELECT p.*, 
           u.username, u.avatar_url,
           ps.likes_count, ps.comments_count
    FROM poems p
    JOIN users u ON p.user_id = u.id
    LEFT JOIN poem_stats ps ON p.id = ps.poem_id
    WHERE p.status = 'approved'
    ORDER BY p.created_at DESC
    LIMIT 20 OFFSET ?
";

// Uses:
// - JOIN on indexed user_id
// - View aggregation (pre-calculated stats)
// - LIMIT/OFFSET for pagination
// - ORDER BY indexed created_at
// Expected execution: ~50-100ms
```

---

## 3. API SPECIFICATION

### 3.1 RESTful API Endpoints

#### Authentication Endpoints

```
POST /api/login.php
Description: User login
Request Body: 
{
    "email": "user@example.com",
    "password": "password123"
}
Response (200):
{
    "success": true,
    "user_id": 1,
    "username": "poet123"
}
Response (401):
{
    "error": "Invalid credentials"
}

POST /api/signup.php
Description: User registration
Request Body:
{
    "username": "newpoet",
    "email": "newpoet@example.com",
    "password": "SecurePass123",
    "full_name": "New Poet"
}
Response (201):
{
    "success": true,
    "user_id": 2,
    "message": "Account created successfully"
}
Response (400):
{
    "error": "Email already exists"
}

POST /api/logout.php
Description: User logout
Response (200):
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### Poem Endpoints

```
POST /api/poems.php
Description: Create new poem
Headers: Content-Type: multipart/form-data
Request Body:
{
    "title": "My First Poem",
    "content": "Poem content here...",
    "format": "text",
    "tags": "love,nature,beauty"
}
Response (201):
{
    "success": true,
    "poem_id": 5,
    "message": "Poem created, awaiting moderation"
}
Response (400):
{
    "error": "Title is required"
}

GET /api/poems.php?id=5
Description: Get poem details
Response (200):
{
    "id": 5,
    "title": "My First Poem",
    "content": "Poem content here...",
    "author": "poet123",
    "format": "text",
    "status": "approved",
    "likes_count": 15,
    "comments_count": 3,
    "views_count": 128,
    "created_at": "2026-02-16 10:30:00"
}
Response (404):
{
    "error": "Poem not found"
}

PATCH /api/patch-poem.php
Description: Update poem
Request Body:
{
    "poem_id": 5,
    "title": "Updated Title",
    "content": "Updated content...",
    "tags": "love,nature"
}
Response (200):
{
    "success": true,
    "message": "Poem updated successfully"
}
Response (403):
{
    "error": "Unauthorized"
}

DELETE /api/delete-poem.php
Description: Delete poem
Request Body:
{
    "poem_id": 5
}
Response (200):
{
    "success": true,
    "message": "Poem deleted"
}
Response (403):
{
    "error": "Unauthorized"
}
```

#### Like Endpoints

```
POST /api/likes.php
Description: Toggle like on poem
Request Body:
{
    "poem_id": 5
}
Response (200):
{
    "success": true,
    "liked": true,
    "likes_count": 16
}
Response (401):
{
    "error": "Unauthorized"
}
```

#### Comment Endpoints

```
POST /api/comments.php
Description: Add comment to poem
Request Body:
{
    "poem_id": 5,
    "content": "Beautiful poem!"
}
Response (201):
{
    "success": true,
    "comment_id": 42,
    "message": "Comment added"
}
Response (400):
{
    "error": "Comment too short"
}

GET /api/comments.php?poem_id=5
Description: Get poem comments
Response (200):
{
    "comments": [
        {
            "id": 42,
            "author": "reader123",
            "content": "Beautiful poem!",
            "created_at": "2026-02-16 11:00:00"
        }
    ]
}

DELETE /api/comments.php
Description: Delete comment
Request Body:
{
    "comment_id": 42
}
Response (200):
{
    "success": true,
    "message": "Comment deleted"
}
```

### 3.2 Error Response Format

```json
{
    "error": "Error message describing what went wrong",
    "error_code": 400,
    "details": {
        "field": "Specific field error if applicable"
    }
}
```

---

## 4. FILE STRUCTURE

### 4.1 Directory Organization

```
poemit/
│
├── README.md                          # Installation guide
├── DOCUMENTATION.md                   # Full project documentation
├── REQUIREMENTS.md                    # Software requirements
├── TECHNICAL_DESIGN.md               # This file
│
├── index.php                          # Home page & poem feed
├── login.php                          # Login page
├── signup.php                         # Registration page
├── write.php                          # Create poem page
├── poem.php                           # View poem details
├── profile.php                        # User profile page
├── admin_dashboard.php                # Admin review panel
├── logout.php                         # Logout handler
│
├── config/
│   ├── config.php                     # Configuration & helpers
│   └── database.php                   # Database connection class
│
├── api/
│   ├── likes.php                      # Like API endpoint
│   ├── comments.php                   # Comments API endpoint
│   ├── poems.php                      # Poems CRUD API
│   ├── patch-poem.php                 # Update poem API
│   └── delete-poem.php                # Delete poem API
│
├── database/
│   └── schema.sql                     # Database schema & init
│
├── includes/
│   ├── header.php                     # Header component
│   └── footer.php                     # Footer component
│
├── assets/
│   ├── css/
│   │   └── style.css                  # Main stylesheet (1500+ lines)
│   ├── js/
│   │   └── app.js                     # JavaScript functionality
│   └── images/
│       └── logo.jpeg                  # Site logo
│
├── public/
│   ├── apple-icon.png                 # PWA icon
│   ├── icon-dark-32x32.png            # Dark icon
│   ├── icon-light-32x32.png           # Light icon
│   ├── icon.svg                       # SVG icon
│   ├── placeholder-logo.png           # Logo placeholder
│   ├── placeholder-logo.svg           # SVG logo
│   ├── placeholder-user.jpg           # User avatar placeholder
│   ├── placeholder.jpg                # Image placeholder
│   └── placeholder.svg                # SVG placeholder
│
├── uploads/                           # User-uploaded files (secure)
│   └── [uploaded poems, avatars]
│
└── .htaccess                          # Apache config (if needed)
```

### 4.2 File Size Overview

| File | Size | Lines | Purpose |
|------|------|-------|---------|
| assets/css/style.css | ~45KB | 1500+ | Complete styling |
| assets/js/app.js | ~20KB | 600+ | Client-side functionality |
| index.php | ~3KB | 60+ | Home page |
| write.php | ~4KB | 80+ | Create poem |
| poem.php | ~5KB | 100+ | View poem |
| config/config.php | ~2KB | 40+ | Configuration |
| database/schema.sql | ~3KB | 80+ | Database schema |

---

## 5. SECURITY ARCHITECTURE

### 5.1 Authentication Flow

```
USER ENTERS CREDENTIALS
        │
        ▼
┌──────────────────────┐
│ Validate Input       │
│ - Email format       │
│ - Password length    │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────────┐
│ Query Database           │
│ - Find user by email     │
│ - Get password_hash      │
└────────┬─────────────────┘
         │
    ┌────┴──────────┐
    │               │
    NO              YES
    │               │
    ▼               ▼
  ERROR    ┌──────────────────┐
           │ Verify Password  │
           │ password_verify()│
           │ (bcrypt)         │
           └────────┬─────────┘
                    │
               ┌────┴──────────┐
               │               │
              NO              YES
              │               │
              ▼               ▼
            ERROR      ┌──────────────────┐
                       │ Create Session   │
                       │ - Set user_id    │
                       │ - Set username   │
                       │ - Set is_admin   │
                       └────────┬─────────┘
                                │
                                ▼
                        SUCCESS - REDIRECT
```

### 5.2 Authorization Matrix

| Role | Can Register | Can Login | Can Post | Can Like | Can Review | Can Admin |
|------|:------------:|:---------:|:--------:|:--------:|:----------:|:---------:|
| Guest | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ |
| User | ✓ | ✓ | ✓ | ✓ | ✗ | ✗ |
| Admin | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |

### 5.3 Security Implementations

#### Input Validation
```php
// File: config/config.php
function validateUsername($username) {
    if (strlen($username) < 3 || strlen($username) > 50) {
        return false;
    }
    return preg_match('/^[a-zA-Z0-9_-]+$/', $username);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password) {
    return strlen($password) >= 8;
}
```

#### Output Escaping
```php
// Prevents XSS attacks
echo escape($user_input);  // htmlspecialchars()

// In HTML
<p><?php echo escape($poem->content); ?></p>
```

#### SQL Injection Prevention
```php
// WRONG - SQL Injection vulnerable
$query = "SELECT * FROM users WHERE email = '$email'";

// CORRECT - Using parameterized queries
$query = "SELECT * FROM users WHERE email = :email";
$stmt = $db->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
```

#### CSRF Protection
```php
// Generate token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

// In forms
<input type="hidden" name="csrf_token" 
       value="<?php echo generateCSRFToken(); ?>">
```

#### Password Hashing
```php
// Create hash (secure)
$password_hash = password_hash($password, PASSWORD_BCRYPT, [
    'cost' => 12  // Security level
]);

// Verify password
if (password_verify($password, $password_hash)) {
    // Password correct
}
```

#### File Upload Security
```php
// Validate file type
$allowed_types = ['image/jpeg', 'image/png'];
if (!in_array($_FILES['file']['type'], $allowed_types)) {
    die('Invalid file type');
}

// Check file size
$max_size = 5242880;  // 5MB
if ($_FILES['file']['size'] > $max_size) {
    die('File too large');
}

// Generate safe filename
$filename = uniqid() . '_' . time() . '.' . 
            strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

// Move to secure location
move_uploaded_file($_FILES['file']['tmp_name'], UPLOAD_DIR . $filename);
```

---

## 6. PERFORMANCE OPTIMIZATION

### 6.1 Database Query Optimization

```php
// BAD: N+1 Query Problem
$poems = $db->query("SELECT * FROM poems")->fetchAll();
foreach ($poems as $poem) {
    $author = $db->query("SELECT * FROM users WHERE id = " . 
                         $poem['user_id'])->fetch();
    // Total: 1 + N queries
}

// GOOD: Single query with JOIN
$query = "
    SELECT p.*, u.username 
    FROM poems p
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'approved'
    ORDER BY p.created_at DESC
    LIMIT 20
";
// Total: 1 query
```

### 6.2 Caching Strategy

```php
// Query caching example (future)
function getCachedPoems($limit = 20) {
    $cache_key = "poems_feed_{$limit}";
    
    // Check cache (if Redis implemented)
    $cached = $cache->get($cache_key);
    if ($cached) {
        return json_decode($cached);
    }
    
    // Query if not cached
    $poems = queryPoems($limit);
    $cache->set($cache_key, json_encode($poems), 300);  // 5 min TTL
    
    return $poems;
}
```

### 6.3 Frontend Performance

```javascript
// Lazy loading for images
const images = document.querySelectorAll('img[data-src]');
const imageObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const img = entry.target;
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
            imageObserver.unobserve(img);
        }
    });
});
images.forEach(img => imageObserver.observe(img));

// Debounced search
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func(...args), wait);
    };
}

const searchInput = document.querySelector('.search');
searchInput.addEventListener('input', debounce(handleSearch, 300));
```

---

## 7. DEPLOYMENT ARCHITECTURE

### 7.1 Development Environment

```
Local Machine
├── XAMPP
│   ├── Apache (localhost:80)
│   ├── MySQL (localhost:3306)
│   ├── phpMyAdmin (localhost/phpmyadmin)
│   └── PHP 7.4+
├── Browser (Chrome/Firefox)
└── Text Editor (VS Code)
```

### 7.2 Production Deployment (Future)

```
Cloud Server / VPS
├── Web Server (Nginx/Apache)
├── Application (PHP-FPM)
├── Database (MySQL/PostgreSQL)
├── Cache (Redis - Optional)
├── CDN (For assets)
└── SSL Certificate (HTTPS)
```

### 7.3 Deployment Checklist

```
Pre-deployment:
☐ All tests passing
☐ Security audit completed
☐ Database optimized
☐ Assets minified (CSS/JS)
☐ Environment variables configured
☐ Backups configured

Deployment:
☐ Files uploaded to server
☐ Database schema initialized
☐ File permissions set (755/644)
☐ .htaccess configured
☐ SSL certificate installed
☐ DNS configured

Post-deployment:
☐ Smoke tests passed
☐ Error logging verified
☐ Monitoring enabled
☐ Backups running
☐ Performance baseline established
```

---

## 8. MONITORING & LOGGING

### 8.1 Error Logging

```php
// File: config/config.php
function logError($message, $level = 'ERROR') {
    $log_file = dirname(__DIR__) . '/logs/app.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message\n";
    error_log($log_entry, 3, $log_file);
}

// Usage
try {
    // Database operation
} catch (Exception $e) {
    logError($e->getMessage(), 'EXCEPTION');
}
```

### 8.2 Performance Monitoring

```php
// Query profiling
$start = microtime(true);
$result = $db->query($query);
$duration = microtime(true) - $start;

if ($duration > 0.5) {
    logError("Slow query: {$duration}s - {$query}", 'PERFORMANCE');
}
```

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Feb 2026 | Initial Technical Design Document |

---

**Document Owner**: Technical Lead  
**Last Updated**: February 2026  
**Next Review**: During development
