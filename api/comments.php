<?php
header('Content-Type: application/json');
require_once '../config/config.php';

// Require authentication
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create comment
    $data = json_decode(file_get_contents('php://input'), true);
    $poem_id = intval($data['poem_id'] ?? 0);
    $content = trim($data['content'] ?? '');
    
    if (!$poem_id || empty($content)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }

    // Insert comment
    $query = "INSERT INTO comments (user_id, poem_id, content) VALUES (:user_id, :poem_id, :content)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->bindParam(':poem_id', $poem_id);
    $stmt->bindParam(':content', $content);
    
    if ($stmt->execute()) {
        $comment_id = $db->lastInsertId();
        
        // Get the new comment with user info
        $query = "SELECT c.*, u.username, u.avatar_url
                  FROM comments c
                  JOIN users u ON c.user_id = u.id
                  WHERE c.id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $comment_id);
        $stmt->execute();
        $comment = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'comment' => $comment
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create comment']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get comments for a poem
    $poem_id = intval($_GET['poem_id'] ?? 0);
    
    if (!$poem_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid poem ID']);
        exit;
    }

    $query = "SELECT c.*, u.username, u.avatar_url
              FROM comments c
              JOIN users u ON c.user_id = u.id
              WHERE c.poem_id = :poem_id
              ORDER BY c.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':poem_id', $poem_id);
    $stmt->execute();
    $comments = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'comments' => $comments
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
