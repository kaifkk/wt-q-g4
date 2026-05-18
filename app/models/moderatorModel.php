<?php
require_once __DIR__ . '/../core/database.php';


function modGetAllContents($search = '', $categoryId = '') {
    $mysqli = getDB();

    $sql = "SELECT c.*, cat.name AS category_name, u.name AS uploader_name
            FROM contents c
            LEFT JOIN categories cat ON c.category_id = cat.id
            LEFT JOIN users u        ON c.uploader_id  = u.id
            WHERE 1=1";

    $params = [];
    $types  = '';

    if ($search !== '') {
        $sql    .= " AND (c.title LIKE ? OR c.description LIKE ?)";
        $like    = '%' . $search . '%';
        $params[] = $like;
        $params[] = $like;
        $types   .= 'ss';
    }

    if ($categoryId !== '' && $categoryId > 0) {
        $sql    .= " AND c.category_id = ?";
        $params[] = (int)$categoryId;
        $types   .= 'i';
    }

    $sql .= " ORDER BY c.uploaded_at DESC";

    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();
    return $rows;
}


function modGetContentById($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare(
        "SELECT c.*, cat.name AS category_name
         FROM contents c
         LEFT JOIN categories cat ON c.category_id = cat.id
         WHERE c.id = ?"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    return $row;
}


function modCreateContent($title, $description, $categoryId, $filePath, $uploaderId) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare(
        "INSERT INTO contents (title, description, category_id, file_path, uploader_id)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('ssisi', $title, $description, $categoryId, $filePath, $uploaderId);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}


function modDeleteContent($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("DELETE FROM contents WHERE id = ?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}


function modGetAllRequests($statusFilter = '') {
    $mysqli = getDB();

    $sql    = "SELECT * FROM content_requests WHERE 1=1";
    $params = [];
    $types  = '';

    if ($statusFilter !== '') {
        $sql    .= " AND status = ?";
        $params[] = $statusFilter;
        $types   .= 's';
    }

    $sql .= " ORDER BY FIELD(status,'pending','fulfilled','rejected'), created_at DESC";

    $stmt = $mysqli->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();
    return $rows;
}

function modUpdateRequestStatus($id, $status) {
    $allowed = ['pending', 'fulfilled', 'rejected'];
    if (!in_array($status, $allowed)) return false;

    $mysqli = getDB();
    $stmt   = $mysqli->prepare("UPDATE content_requests SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $status, $id);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}


function modCountPendingRequests() {
    $mysqli = getDB();
    $result = $mysqli->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status = 'pending'");
    $cnt    = $result->fetch_assoc()['cnt'] ?? 0;
    $mysqli->close();
    return (int)$cnt;
}

function modGetDashboardStats() {
    $mysqli = getDB();
    $stats  = [];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM contents");
    $stats['total_contents'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status = 'pending'");
    $stats['pending_requests'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status = 'fulfilled'");
    $stats['fulfilled_requests'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status = 'rejected'");
    $stats['rejected_requests'] = $r->fetch_assoc()['cnt'];

    $mysqli->close();
    return $stats;
}
?>