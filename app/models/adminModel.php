<?php
require_once __DIR__ . '/../core/database.php';


function getAllModerators() {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare(
        "SELECT id, name, email, created_at FROM users WHERE role = 'moderator' ORDER BY created_at DESC"
    );
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $mysqli->close();
    return $rows;
}

function moderatorExists($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("SELECT id FROM users WHERE id = ? AND role = 'moderator'");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    return $row !== null;
}

function deleteModerator($id) {
    $mysqli = getDB();

    $stmt = $mysqli->prepare("UPDATE contents SET uploader_id = NULL WHERE uploader_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'moderator'");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}


function getAllContentsWithUploader() {
    $mysqli = getDB();
    $sql = "SELECT c.*, cat.name AS category_name, u.name AS uploader_name
            FROM contents c
            LEFT JOIN categories cat ON c.category_id = cat.id
            LEFT JOIN users u        ON c.uploader_id  = u.id
            ORDER BY c.uploaded_at DESC";
    $result   = $mysqli->query($sql);
    $contents = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }
    }
    $mysqli->close();
    return $contents;
}

function getContentById($id) {
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

function createContent($title, $description, $categoryId, $filePath, $uploaderId) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare(
        "INSERT INTO contents (title, description, category_id, file_path, uploader_id) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param('ssisi', $title, $description, $categoryId, $filePath, $uploaderId);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}

function updateContent($id, $title, $description, $categoryId, $filePath = null) {
    $mysqli = getDB();
    if ($filePath !== null) {
        $stmt = $mysqli->prepare(
            "UPDATE contents SET title=?, description=?, category_id=?, file_path=? WHERE id=?"
        );
        $stmt->bind_param('ssisi', $title, $description, $categoryId, $filePath, $id);
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE contents SET title=?, description=?, category_id=? WHERE id=?"
        );
        $stmt->bind_param('ssii', $title, $description, $categoryId, $id);
    }
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}

function deleteContent($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("DELETE FROM contents WHERE id=?");
    $stmt->bind_param('i', $id);
    $ok = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $ok;
}

function getAdminDashboardStats() {
    $mysqli = getDB();
    $stats  = [];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM contents");
    $stats['total_contents'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM categories");
    $stats['total_categories'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM users WHERE role='moderator'");
    $stats['total_moderators'] = $r->fetch_assoc()['cnt'];

    $r = $mysqli->query("SELECT COUNT(*) AS cnt FROM content_requests WHERE status='pending'");
    $stats['pending_requests'] = $r->fetch_assoc()['cnt'];

    $mysqli->close();
    return $stats;
}


function getCategoriesGrouped() {
    $mysqli = getDB();
    $result = $mysqli->query(
        "SELECT * FROM categories ORDER BY parent_id ASC, name ASC"
    );
    $all = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $all[] = $row;
        }
    }
    $mysqli->close();

    $parents  = [];
    $children = [];
    foreach ($all as $cat) {
        if ($cat['parent_id'] === null) {
            $parents[$cat['id']] = $cat;
        } else {
            $children[$cat['parent_id']][] = $cat;
        }
    }
    return ['parents' => $parents, 'children' => $children];
}
?>
