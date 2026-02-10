<?php
require_once 'config/config.php';

$username = trim($_GET['user'] ?? '');

if (empty($username)) {
    redirect('/');
}

$database = new Database();
$db = $database->getConnection();

// Get user info
$query = "SELECT id, username, full_name, bio, avatar_url, created_at FROM users WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch();

if (!$user) {
    redirect('/');
}

// Get user's poems
$query = "SELECT p.*, ps.likes_count, ps.comments_count
          FROM poems p
          LEFT JOIN poem_stats ps ON p.id = ps.poem_id
          WHERE p.user_id = :user_id AND p.is_published = 1
          ORDER BY p.created_at DESC";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$poems = $stmt->fetchAll();

// Get user stats
$query = "SELECT 
          COUNT(DISTINCT p.id) as poems_count,
          COALESCE(SUM(ps.likes_count), 0) as total_likes
          FROM poems p
          LEFT JOIN poem_stats ps ON p.id = ps.poem_id
          WHERE p.user_id = :user_id AND p.is_published = 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user['id']);
$stmt->execute();
$stats = $stmt->fetch();

$page_title = escape($user['username']) . ' - ' . SITE_NAME;
include 'includes/header.php';
?>

<div class="container profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h1 class="profile-name"><?php echo escape($user['full_name'] ?: $user['username']); ?></h1>
            <p class="profile-username">@<?php echo escape($user['username']); ?></p>
            <?php if ($user['bio']): ?>
                <p class="profile-bio"><?php echo nl2br(escape($user['bio'])); ?></p>
            <?php endif; ?>
            <div class="profile-stats">
                <span><strong><?php echo $stats['poems_count']; ?></strong> poems</span>
                <span><strong><?php echo $stats['total_likes']; ?></strong> likes</span>
                <span>Joined <?php echo date('F Y', strtotime($user['created_at'])); ?></span>
            </div>
        </div>
    </div>

    <div class="profile-poems">
        <h2>Poems by <?php echo escape($user['username']); ?></h2>
        
        <?php if (empty($poems)): ?>
            <div class="empty-state">
                <p>No poems yet</p>
            </div>
        <?php else: ?>
            <div class="poems-grid">
                <?php foreach ($poems as $poem): ?>
                    <article class="poem-card">
                        <div class="poem-card-header">
                            <div class="poem-date">
                                <?php echo date('M j, Y', strtotime($poem['created_at'])); ?>
                            </div>
                        </div>

                        <a href="poem?id=<?php echo $poem['id']; ?>" class="poem-link">
                            <h2 class="poem-title"><?php echo escape($poem['title']); ?></h2>
                            
                            <?php if ($poem['format'] === 'text'): ?>
                                <div class="poem-content">
                                    <?php 
                                    $content = escape($poem['content']);
                                    echo strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
                                    ?>
                                </div>
                            <?php elseif ($poem['format'] === 'image'): ?>
                                <div class="poem-image">
                                    <img src="<?php echo escape($poem['file_url']); ?>" alt="<?php echo escape($poem['title']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="poem-document">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                    </svg>
                                    <span>Document poem</span>
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="poem-card-footer">
                            <span class="poem-stat">
                                ❤️ <?php echo $poem['likes_count'] ?? 0; ?>
                            </span>
                            <span class="poem-stat">
                                💬 <?php echo $poem['comments_count'] ?? 0; ?>
                            </span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
