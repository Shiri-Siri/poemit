<?php
require_once 'config/config.php';

$poem_id = intval($_GET['id'] ?? 0);

if (!$poem_id) {
    redirect('/');
}

$database = new Database();
$db = $database->getConnection();

// Get poem with author info
$query = "SELECT p.*, u.username, u.avatar_url, u.bio,
          ps.likes_count, ps.comments_count
          FROM poems p
          JOIN users u ON p.user_id = u.id
          LEFT JOIN poem_stats ps ON p.id = ps.poem_id
          WHERE p.id = :id AND p.is_published = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $poem_id);
$stmt->execute();
$poem = $stmt->fetch();

if (!$poem) {
    redirect('/');
}

// Increment view count
$query = "UPDATE poems SET views_count = views_count + 1 WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $poem_id);
$stmt->execute();

// Check if current user liked this poem
$user_liked = false;
if (isLoggedIn()) {
    $query = "SELECT id FROM likes WHERE user_id = :user_id AND poem_id = :poem_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':poem_id', $poem_id);
    $stmt->execute();
    $user_liked = $stmt->fetch() !== false;
}

// Get comments
$query = "SELECT c.*, u.username, u.avatar_url
          FROM comments c
          JOIN users u ON c.user_id = u.id
          WHERE c.poem_id = :poem_id
          ORDER BY c.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':poem_id', $poem_id);
$stmt->execute();
$comments = $stmt->fetchAll();

$page_title = escape($poem['title']) . ' - ' . SITE_NAME;
include 'includes/header.php';
?>

<div class="container poem-detail-container">
    <div class="poem-detail">
        <div class="poem-detail-header">
            <h1 class="poem-detail-title"><?php echo escape($poem['title']); ?></h1>
            
            <div class="author-card">
                <div class="author-avatar-large">
                    <?php echo strtoupper(substr($poem['username'], 0, 1)); ?>
                </div>
                <div class="author-details">
                    <a href="profile?user=<?php echo escape($poem['username']); ?>" class="author-name-large">
                        <?php echo escape($poem['username']); ?>
                    </a>
                    <div class="poem-meta">
                        <?php echo date('F j, Y', strtotime($poem['created_at'])); ?> • 
                        <?php echo $poem['views_count']; ?> views
                    </div>
                </div>
            </div>
        </div>

        <div class="poem-detail-content">
            <?php if ($poem['format'] === 'text'): ?>
                <div class="poem-text">
                    <?php echo nl2br(escape($poem['content'])); ?>
                </div>
            <?php elseif ($poem['format'] === 'image'): ?>
                <div class="poem-image-full">
                    <img src="<?php echo escape($poem['file_url']); ?>" alt="<?php echo escape($poem['title']); ?>">
                </div>
            <?php else: ?>
                <div class="poem-document-full">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <h3>Document Poem</h3>
                    <a href="<?php echo escape($poem['file_url']); ?>" download class="btn btn-primary">
                        Download Document
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($poem['tags']): ?>
                <div class="poem-tags">
                    <?php foreach (explode(',', $poem['tags']) as $tag): ?>
                        <span class="tag"><?php echo escape(trim($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="poem-actions">
            <?php if (isLoggedIn()): ?>
                <button 
                    class="btn <?php echo $user_liked ? 'btn-liked' : 'btn-secondary'; ?>" 
                    onclick="toggleLike(<?php echo $poem['id']; ?>)"
                    id="likeBtn"
                >
                    ❤️ <span id="likeCount"><?php echo $poem['likes_count'] ?? 0; ?></span>
                </button>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary">
                    ❤️ <?php echo $poem['likes_count'] ?? 0; ?>
                </a>
            <?php endif; ?>
            <span class="poem-stat-large">
                💬 <?php echo $poem['comments_count'] ?? 0; ?> comments
            </span>
        </div>

        <div class="comments-section">
            <h2>Comments</h2>

            <?php if (isLoggedIn()): ?>
                <form class="comment-form" onsubmit="submitComment(event, <?php echo $poem['id']; ?>)">
                    <textarea 
                        id="commentContent" 
                        placeholder="Share your thoughts..." 
                        rows="3" 
                        required
                    ></textarea>
                    <button type="submit" class="btn btn-primary">Post Comment</button>
                </form>
            <?php else: ?>
                <div class="alert">
                    <a href="login.php">Login</a> to comment on this poem
                </div>
            <?php endif; ?>

            <div id="commentsList" class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <div class="author-avatar-small">
                                <?php echo strtoupper(substr($comment['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <a href="profile?user=<?php echo escape($comment['username']); ?>" class="comment-author">
                                    <?php echo escape($comment['username']); ?>
                                </a>
                                <div class="comment-date">
                                    <?php echo date('M j, Y \a\t g:i A', strtotime($comment['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(escape($comment['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
const POEM_ID = <?php echo $poem['id']; ?>;
const BASE_URL = '<?php echo BASE_URL; ?>';
let isLiked = <?php echo $user_liked ? 'true' : 'false'; ?>;
</script>

<?php include 'includes/footer.php'; ?>
