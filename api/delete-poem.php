<?php
require_once '../config/config.php';
header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success'=>false,'message'=>'Login required']); exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$poem_id = intval($data['poem_id'] ?? 0);
if (!$poem_id) {
    echo json_encode(['success'=>false,'message'=>'Invalid poem']); exit;
}

$db = (new Database())->getConnection();

/* Verify ownership */
$stmt = $db->prepare("SELECT user_id FROM poems WHERE id=:id");
$stmt->execute([':id'=>$poem_id]);
$poem = $stmt->fetch();

if (!$poem || $poem['user_id'] != $_SESSION['user_id']) {
    echo json_encode(['success'=>false,'message'=>'Unauthorized']); exit;
}

/* Delete poem */
$db->prepare("DELETE FROM poems WHERE id=:id")->execute([':id'=>$poem_id]);

echo json_encode(['success'=>true]);
