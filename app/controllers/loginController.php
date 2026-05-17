<?php
require '../core/session.php';
require '../models/userModel.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $user = findUserByEmail($email);

    if($user && password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        if(isset($_POST['remember_me'])) {

            $token = bin2hex(random_bytes(32));

            saveRememberToken(
                $user['id'],
                $token
            );

            setcookie(
                'remember_token',
                $token,
                time() + (86400 * 30),
                '/'
            );
        }

        header('Location: ../../index.php');
        exit();
    }

    $_SESSION['error'] = 'Invalid email or password';

    header('Location: ../views/login.php');
    exit();
}
?>