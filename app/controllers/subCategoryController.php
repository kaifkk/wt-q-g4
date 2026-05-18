<?php
header('Content-Type: application/json');
require_once '../models/contentModel.php';
require_once '../core/validation.php'; 

$catId = isset($_GET['category_id']) ? clean($_GET['category_id']) : '';

if ($catId !== '') {
    $results = getSubCategories($catId);
    echo json_encode(['status' => 'success', 'data' => $results]);
} else {
    echo json_encode(['status' => 'success', 'data' => []]);
}
?>