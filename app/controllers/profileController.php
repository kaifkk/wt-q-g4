<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../views/login.php');
    exit();
}

require_once '../models/userModel.php';
require_once '../core/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/profile.php');
    exit();
}

$action = $_POST['action'] ?? '';
$userId = (int) $_SESSION['user_id'];

if ($action === 'update_profile') {
    $name  = clean($_POST['name']  ?? '');
    $email = clean($_POST['email'] ?? '');
    $errors = [];

    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }

    $existing = findUserByEmail($email);
    if ($existing && (int)$existing['id'] !== $userId) {
        $errors[] = 'That email is already in use by another account.';
    }

    $picturePath = null;

    if (!empty($_FILES['profile_picture']['name'])) {
        $file     = $_FILES['profile_picture'];
        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxBytes = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Profile picture must be a JPEG, PNG, GIF, or WebP image.';
        } elseif ($file['size'] > $maxBytes) {
            $errors[] = 'Profile picture must be under 2 MB.';
        } elseif ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'File upload failed (error code ' . $file['error'] . ').';
        } else {
            $ext         = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename    = 'avatar_' . $userId . '_' . time() . '.' . strtolower($ext);
            $uploadDir   = __DIR__ . '/../public/assets/uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $picturePath = $filename;

                $old = findUserById($userId);
                if ($old && !empty($old['profile_picture'])) {
                    $oldPath = $uploadDir . $old['profile_picture'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }
            } else {
                $errors[] = 'Could not save uploaded file.';
                echo $uploadDir;
                exit;
            }
        }
    }

    if (!empty($errors)) {
        $_SESSION['profile_errors'] = $errors;
        header('Location: ../views/profile.php');
        exit();
    }

    if (updateProfile($userId, $name, $email, $picturePath)) {
        $_SESSION['name'] = $name;
        $_SESSION['profile_success'] = 'Profile updated successfully.';
    } else {
        $_SESSION['profile_errors'] = ['Failed to update profile. Please try again.'];
    }

    header('Location: ../views/profile.php');
    exit();
}

if ($action === 'change_password') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password']     ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $errors  = [];

    if (empty($current)) {
        $errors[] = 'Current password is required.';
    }
    if (strlen($new) < 8) {
        $errors[] = 'New password must be at least 8 characters.';
    }
    if ($new !== $confirm) {
        $errors[] = 'New passwords do not match.';
    }

    if (!empty($errors)) {
        $_SESSION['password_errors'] = $errors;
        header('Location: ../views/profile.php');
        exit();
    }

    $result = updatePassword($userId, $current, $new);

    if ($result === true) {
        $_SESSION['password_success'] = 'Password changed successfully.';
    } else {
        $_SESSION['password_errors'] = [$result];
    }

    header('Location: ../views/profile.php');
    exit();
}

header('Location: ../views/profile.php');
exit();
?>