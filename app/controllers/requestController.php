<?php
header('Content-Type: application/json');
require_once '../models/requestModel.php';
require_once '../core/validation.php'; // Using Person 1's exact validation file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Using Person 1's clean() function
    $title = clean($_POST['title'] ?? '');
    $category = clean($_POST['category'] ?? '');
    $message = clean($_POST['message'] ?? '');
    
    $ip = $_SERVER['REMOTE_ADDR']; 

    $errors = [];
    if (empty($title)) $errors['title'] = "Title is required.";
    if (empty($category)) $errors['category'] = "Category is required.";

    if (count($errors) > 0) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
    } else {
        if (createContentRequest($ip, $title, $category, $message)) {
            echo json_encode(['status' => 'success', 'msg' => 'Content request submitted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Failed to submit request.']);
        }
    }
}
?>