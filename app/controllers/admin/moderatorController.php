<?php
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/userModel.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../core/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../views/admin/manage_moderators.php');
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $name     = clean($_POST['name']             ?? '');
    $email    = clean($_POST['email']            ?? '');
    $password = $_POST['password']               ?? '';
    $confirm  = $_POST['confirm_password']       ?? '';
    $errors   = [];

    if (empty($name)) {
        $errors[] = 'Full name is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    } elseif (findUserByEmail($email)) {
        $errors[] = 'That email is already registered.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!empty($errors)) {
        $_SESSION['mod_errors'] = $errors;
        header('Location: ../../views/admin/manage_moderators.php');
        exit();
    }

    if (createUser($name, $email, $password, 'moderator')) {
        $_SESSION['mod_success'] = "Moderator \"$name\" added successfully.";
    } else {
        $_SESSION['mod_errors'] = ['Failed to add moderator. Please try again.'];
    }

    header('Location: ../../views/admin/manage_moderators.php');
    exit();
}

if ($action === 'delete') {
    $modId = (int)($_POST['mod_id'] ?? 0);

    if ($modId <= 0 || !moderatorExists($modId)) {
        $_SESSION['mod_errors'] = ['Invalid moderator ID.'];
        header('Location: ../../views/admin/manage_moderators.php');
        exit();
    }

    if (deleteModerator($modId)) {
        $_SESSION['mod_success'] = 'Moderator deleted. Their contents have been reassigned.';
    } else {
        $_SESSION['mod_errors'] = ['Failed to delete moderator.'];
    }

    header('Location: ../../views/admin/manage_moderators.php');
    exit();
}

header('Location: ../../views/admin/manage_moderators.php');
exit();
?>
