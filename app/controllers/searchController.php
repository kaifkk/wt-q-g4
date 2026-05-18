<?php
header('Content-Type: application/json');
require_once '../models/contentModel.php';
require_once '../core/validation.php'; 

$q = isset($_GET['q']) ? clean($_GET['q']) : '';
$cat = isset($_GET['category']) ? clean($_GET['category']) : '';
$subCat = isset($_GET['sub_category']) ? clean($_GET['sub_category']) : '';


$results = searchContents($q, $cat, $subCat);
echo json_encode(['status' => 'success', 'data' => $results]);
?>