<?php
session_start();
require '../models/userModel.php';
require '../core/validation.php';

$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if(empty($name)) {
        $errors['name'] = 'Name Required';
    }

    $existing = findUserByEmail($email);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid Email';
    } elseif($existing) {
        $errors['email'] = 'Email Already Exists';
    }

    if(strlen($password) < 8) {
        $errors['password'] =
            'Password minimum 8 characters';
    }

    if(empty($errors)) {

        createUser(
            $name,
            $email,
            $password,
            $role
        );

        $_SESSION['success'] =
            'Registration Successful';

        header('Location: ../views/login.php');
        exit();
    } else {
        $_SESSION['errors'] = $errors;
        header('Location: ../views/register.php');
        exit();
    }
}

?>