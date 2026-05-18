<?php
header('Content-Type: application/json');
require_once '../models/contentModel.php';
require_once '../core/validation.php';

$q   = isset($_GET['q'])        ? clean($_GET['q'])        : '';
$cat = isset($_GET['category']) ? clean($_GET['category']) : '';

if ($q === '' && $cat === '') {
    $results = getHighlightedContents();
    echo json_encode(['status' => 'success', 'mode' => 'highlighted', 'data' => $results]);
} else {
    $results = searchContents($q, $cat);
    echo json_encode(['status' => 'success', 'mode' => 'search', 'data' => $results]);
}
?>