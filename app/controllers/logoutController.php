<?php
session_start();

if (isset($_COOKIE['remember_token']) && isset($_SESSION['user_id'])) {
    require_once '../models/userModel.php';
    clearRememberToken((int) $_SESSION['user_id']);
    setcookie('remember_token', '', time() - 3600, '/');
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '',
        time() - 3600,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../index.php');
exit();
?>