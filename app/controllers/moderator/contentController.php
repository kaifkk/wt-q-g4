<?php
require_once __DIR__ . '/../../core/moderatorGate.php';
require_once __DIR__ . '/../../models/moderatorModel.php';
require_once __DIR__ . '/../../core/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/moderator/manage_contents.php');
    exit();
}

$action = $_POST['action'] ?? '';

$ALLOWED_EXTS = ['zip','rar','7z','tar','gz','mp4','mkv','avi','mov',
                 'mp3','flac','wav','exe','msi','iso','apk','pdf','txt'];
$MAX_BYTES    = 200 * 1024 * 1024;
$UPLOAD_DIR   = dir(__DIR__, 2) . '/public/uploads/contents/';

function modHandleFileUpload($fileKey, $allowedExts, $maxBytes, $uploadDir) {
    if (empty($_FILES[$fileKey]['name'])) {
        return ['path' => null, 'error' => null];
    }

    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['path' => null, 'error' => 'File upload failed (error code ' . $file['error'] . ').'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts)) {
        return ['path' => null, 'error' => 'File type .' . $ext . ' is not allowed.'];
    }
    if ($file['size'] > $maxBytes) {
        return ['path' => null, 'error' => 'File exceeds the 200 MB limit.'];
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('content_', true) . '.' . $ext;
    $dest     = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['path' => null, 'error' => 'Could not save uploaded file.'];
    }

    return ['path' => 'public/uploads/contents/' . $filename, 'error' => null];
}

if ($action === 'upload') {
    $title      = clean($_POST['title']       ?? '');
    $desc       = clean($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $uploaderId = (int)$_SESSION['user_id'];
    $errors     = [];

    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Please select a category.';
    }

    $upload = modHandleFileUpload('content_file', $ALLOWED_EXTS, $MAX_BYTES, $UPLOAD_DIR);
    if ($upload['error']) {
        $errors[] = $upload['error'];
    }
    if ($upload['path'] === null && empty($upload['error'])) {
        $errors[] = 'Please select a file to upload.';
    }

    if (!empty($errors)) {
        $_SESSION['mod_content_errors'] = $errors;
        header('Location: ../../views/moderator/upload_content.php');
        exit();
    }

    if (modCreateContent($title, $desc, $categoryId, $upload['path'], $uploaderId)) {
        $_SESSION['mod_content_success'] = 'Content "' . $title . '" uploaded successfully.';
        header('Location: ../../views/moderator/manage_contents.php');
    } else {
        $_SESSION['mod_content_errors'] = ['Database error — could not save content.'];
        header('Location: ../../views/moderator/upload_content.php');
    }
    exit();
}

if ($action === 'delete') {
    header('Content-Type: application/json');

    $contentId = (int)($_POST['content_id'] ?? 0);
    $content   = $contentId ? modGetContentById($contentId) : null;

    if (!$content) {
        echo json_encode(['success' => false, 'message' => 'Content not found.']);
        exit();
    }

    if (!empty($content['file_path'])) {
        $fullPath = __DIR__ . '/../../' . $content['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    if (modDeleteContent($contentId)) {
        echo json_encode(['success' => true, 'message' => 'Content "' . htmlspecialchars($content['title']) . '" deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete content from database.']);
    }
    exit();
}

header('Location: ../../views/moderator/manage_contents.php');
exit();
?>