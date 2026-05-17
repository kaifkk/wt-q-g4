<?php

header('Content-Type: application/json');

require_once '../models/userModel.php';

$email = $_GET['email'];

$user = findUserByEmail($email);

echo json_encode([
    'exists' => $user ? true : false
]);
?>