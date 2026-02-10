<?php
require_once 'config/config.php';

// Require authentication
if (!isLoggedIn()) {
    redirect('/login');
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $format = $_POST['format'] ?? 'text';
    $tags = trim($_POST['tags'] ?? '');
    $file_url = null;

    // Validation
    if (empty($title)) {
        $error = 'Title is required';
    } elseif ($format === 'text' && empty($content)) {
        $error = 'Content is required for text poems';
    } elseif (($format === 'image' || $format === 'document') && empty($_FILES['file']['name'])) {
        $error = 'File is required for ' . $format . ' poems';
    } else {
        // Handle file upload
        if ($format !== 'text' && !empty($_FILES['file']['name'])) {
            $file = $_FILES['file'];
            
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = 'File upload failed with error code: ' . $file['error'];
            } elseif ($file['size'] > MAX_FILE_SIZE) {
                $error = 'File size exceeds 5MB limit';
            } else {
                $allowed_types = $format === 'image' ? ALLOWED_IMAGE_TYPES : ALLOWED_DOC_TYPES;
                
                if (!in_array($file['type'], $allowed_types)) {
                    $error = 'Invalid file type. Allowed types: ' . implode(', ', $allowed_types);
                } else {
                    // Generate unique filename
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '_' . time() . '.' . $extension;
                    $upload_path = UPLOAD_DIR . $filename;
                    
                    // Create upload directory if it doesn't exist
                    if (!is_dir(UPLOAD_DIR)) {
                        if (!mkdir(UPLOAD_DIR, 0755, true)) {
                            $error = 'Failed to create uploads directory. Check folder permissions.';
                        }
                    }
                    
                    if (empty($error) && move_uploaded_file($file['tmp_name'], $upload_path)) {
                        $file_url = UPLOAD_URL . $filename;
                    } else if (empty($error)) {
                        $error = 'Failed to save file. Check uploads folder permissions.';
                    }
                }
            }
        }

        // Save poem if no errors
        if (empty($error)) {
            $database = new Database();
            $db = $database->getConnection();

            $query = "INSERT INTO poems (user_id, title, content, format, file_url, tags) 
                      VALUES (:user_id, :title, :content, :format, :file_url, :tags)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':format', $format);
            $stmt->bindParam(':file_url', $file_url);
            $stmt->bindParam(':tags', $tags);

            if ($stmt->execute()) {
                $poem_id = $db->lastInsertId();
                redirect('/poem?id=' . $poem_id);
            } else {
                $error = 'Failed to create poem';
            }
        }
    }
}

$page_title = 'Write a Poem - ' . SITE_NAME;
include 'includes/header.php';
?>

<div class="container write-container">
    <div class="write-header">
        <h1>Write Your Poem</h1>
        <p>Share your creativity with the world</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo escape($error); ?></div>
    <?php endif; ?>

    <div class="write-card">
        <form method="POST" action="write.php" enctype="multipart/form-data" id="poemForm">
            <div class="form-group">
                <label for="title">Title *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required 
                    placeholder="Give your poem a title"
                    value="<?php echo escape($_POST['title'] ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label>Format *</label>
                <div class="format-tabs">
                    <button type="button" class="format-tab active" data-format="text">Text</button>
                    <button type="button" class="format-tab" data-format="image">Image</button>
                    <button type="button" class="format-tab" data-format="document">Document</button>
                </div>
                <input type="hidden" name="format" id="format" value="text">
            </div>

            <div id="textFormat" class="format-content active">
                <div class="form-group">
                    <label for="content">Your Poem *</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="12" 
                        placeholder="Write your poem here..."
                    ><?php echo escape($_POST['content'] ?? ''); ?></textarea>
                </div>
            </div>

            <div id="imageFormat" class="format-content">
                <div class="form-group">
                    <label for="imageFile">Upload Image *</label>
                    <input 
                        type="file" 
                        id="imageFile" 
                        name="file" 
                        accept="image/*"
                    >
                    <small>Max size: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                </div>
            </div>

            <div id="documentFormat" class="format-content">
                <div class="form-group">
                    <label for="documentFile">Upload Document *</label>
                    <input 
                        type="file" 
                        id="documentFile" 
                        name="file" 
                        accept=".pdf,.doc,.docx"
                    >
                    <small>Max size: 5MB. Formats: PDF, DOC, DOCX</small>
                </div>
            </div>

            <div class="form-group">
                <label for="tags">Tags</label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags" 
                    placeholder="e.g., nature, love, haiku"
                    value="<?php echo escape($_POST['tags'] ?? ''); ?>"
                >
                <small>Separate tags with commas</small>
            </div>

            <div class="form-actions">
                <a href="<?php echo BASE_URL; ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Publish Poem</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
