<?php
require_once __DIR__ . '/../../core/moderatorGate.php';
require_once __DIR__ . '/../../models/moderatorModel.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$requestId = (int)($_POST['request_id'] ?? 0);
$status    = trim($_POST['status'] ?? '');

if ($requestId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid request ID.']);
    exit();
}

if (!in_array($status, ['pending', 'fulfilled', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit();
}

if (modUpdateRequestStatus($requestId, $status)) {
    echo json_encode([
        'success' => true,
        'message' => 'Status updated to "' . $status . '".',
        'new_status' => $status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}
exit();
?>