<?php
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../core/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/admin/manage_contents.php');
    exit();
}

$action = $_POST['action'] ?? '';

$ALLOWED_EXTS  = ['zip','rar','7z','tar','gz','mp4','mkv','avi','mov',
                  'mp3','flac','wav','exe','msi','iso','apk','pdf','txt'];
$MAX_BYTES     = 200 * 1024 * 1024;

$UPLOAD_DIR = dirname(__DIR__, 2) . '/public/assets/uploads/contents/';

function handleFileUpload($fileKey, $allowedExts, $maxBytes, $uploadDir) {
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
        die($dest);
    }

    return ['path' => 'public/assets/uploads/contents/' . $filename, 'error' => null];
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

    $upload = handleFileUpload('content_file', $ALLOWED_EXTS, $MAX_BYTES, $UPLOAD_DIR);
    if ($upload['error']) {
        $errors[] = $upload['error'];
    }
    if ($upload['path'] === null && empty($upload['error'])) {
        $errors[] = 'Please select a file to upload.';
    }

    if (!empty($errors)) {
        $_SESSION['content_errors'] = $errors;
        header('Location: ../../views/admin/upload_content.php');
        exit();
    }

    if (createContent($title, $desc, $categoryId, $upload['path'], $uploaderId)) {
        $_SESSION['content_success'] = 'Content "' . $title . '" uploaded successfully.';
        header('Location: ../../views/admin/manage_contents.php');
    } else {
        $_SESSION['content_errors'] = ['Database error — could not save content.'];
        header('Location: ../../views/admin/upload_content.php');
    }
    exit();
}

if ($action === 'edit') {
    $contentId  = (int)($_POST['content_id'] ?? 0);
    $title      = clean($_POST['title']       ?? '');
    $desc       = clean($_POST['description'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $errors     = [];

    $existing = $contentId ? getContentById($contentId) : null;
    if (!$existing) {
        $_SESSION['content_errors'] = ['Content not found.'];
        header('Location: ../../views/admin/manage_contents.php');
        exit();
    }

    if (empty($title)) {
        $errors[] = 'Title is required.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Please select a category.';
    }

    $newFilePath = null;
    $upload      = handleFileUpload('content_file', $ALLOWED_EXTS, $MAX_BYTES, $UPLOAD_DIR);
    if ($upload['error']) {
        $errors[] = $upload['error'];
    } elseif ($upload['path'] !== null) {
        $newFilePath = $upload['path'];
    }

    if (!empty($errors)) {
        $_SESSION['content_errors'] = $errors;
        header('Location: ../../views/admin/edit_content.php?id=' . $contentId);
        exit();
    }

    if ($newFilePath && !empty($existing['file_path'])) {
        $oldFull = __DIR__ . '/../../' . $existing['file_path'];
        if (file_exists($oldFull)) {
            unlink($oldFull);
        }
    }

    if (updateContent($contentId, $title, $desc, $categoryId, $newFilePath)) {
        $_SESSION['content_success'] = 'Content updated successfully.';
        header('Location: ../../views/admin/manage_contents.php');
    } else {
        $_SESSION['content_errors'] = ['Failed to update content.'];
        header('Location: ../../views/admin/edit_content.php?id=' . $contentId);
    }
    exit();
}

if ($action === 'delete') {
    $contentId = (int)($_POST['content_id'] ?? 0);
    $content   = $contentId ? getContentById($contentId) : null;

    if (!$content) {
        $_SESSION['content_errors'] = ['Content not found.'];
        header('Location: ../../views/admin/manage_contents.php');
        exit();
    }

    if (!empty($content['file_path'])) {
        $fullPath = __DIR__ . '/../../' . $content['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    if (deleteContent($contentId)) {
        $_SESSION['content_success'] = 'Content "' . htmlspecialchars($content['title']) . '" deleted.';
    } else {
        $_SESSION['content_errors'] = ['Failed to delete content from database.'];
    }

    header('Location: ../../views/admin/manage_contents.php');
    exit();
}

header('Location: ../../views/admin/manage_contents.php');
exit();
?>
