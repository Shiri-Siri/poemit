<?php
require_once '../config/config.php';
header('Content-Type: application/json');

// Check login
if(!isLoggedIn()){
    echo json_encode(['success'=>false,'message'=>'Not logged in']);
    exit;
}

// Validate POST and poem_id
$poem_id = $_POST['poem_id'] ?? null;
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if(!$poem_id){
    echo json_encode(['success'=>false,'message'=>'Poem ID required']);
    exit;
}

$db = (new Database())->getConnection();

// Check ownership
$stmt = $db->prepare("SELECT * FROM poems WHERE id=:id AND user_id=:uid");
$stmt->execute([':id'=>$poem_id, ':uid'=>$_SESSION['user_id']]);
$poem = $stmt->fetch();
if(!$poem){
    echo json_encode(['success'=>false,'message'=>'Poem not found or unauthorized']);
    exit;
}

// Prepare update
$updateFields = [];
$params = [':id'=>$poem_id];

if($title !== ''){
    $updateFields[] = 'title = :title';
    $params[':title'] = $title;
}

if($content !== ''){
    $updateFields[] = 'content = :content';
    $params[':content'] = $content;
}

// Handle file upload
if(isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK){
    $fileTmp = $_FILES['file']['tmp_name'];
    $fileName = basename($_FILES['file']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedImages = ['jpg','jpeg','png','gif'];
    $allowedDocs   = ['pdf','doc','docx','txt'];

    $uploadDir = '../uploads/';
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newFileName = uniqid().'_'.preg_replace('/[^a-zA-Z0-9_.-]/','_',$fileName);
    $dest = $uploadDir.$newFileName;

    if(!move_uploaded_file($fileTmp,$dest)){
        echo json_encode(['success'=>false,'message'=>'File upload failed']);
        exit;
    }

    // Update poem format and file_url
    if(in_array($fileExt,$allowedImages)){
        $updateFields[] = 'format="image"';
        $updateFields[] = 'file_url=:file_url';
        $params[':file_url'] = BASE_URL.'/uploads/'.$newFileName;
    } elseif(in_array($fileExt,$allowedDocs)){
        $updateFields[] = 'format="document"';
        $updateFields[] = 'file_url=:file_url';
        $params[':file_url'] = BASE_URL.'/uploads/'.$newFileName;
    } else {
        echo json_encode(['success'=>false,'message'=>'Invalid file type']);
        exit;
    }
}

// Run update
if(!empty($updateFields)){
    $sql = "UPDATE poems SET ".implode(', ',$updateFields)." WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
}

echo json_encode(['success'=>true,'message'=>'Poem updated successfully']);
