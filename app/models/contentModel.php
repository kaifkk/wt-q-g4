<?php
require_once __DIR__ . '/../core/database.php';

function getAllCategories() {
    $mysqli = getDB();
    $sql = "SELECT * FROM categories ORDER BY name ASC";
    $result = $mysqli->query($sql);
    
    $categories = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    $mysqli->close();
    return $categories;
}

function searchContents($query, $categoryId = '') {
    $mysqli = getDB();
    $sql = "SELECT c.*, cat.name as category_name FROM contents c 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE (c.title LIKE ? OR c.description LIKE ?)";
            
    if (!empty($categoryId)) {
        $sql .= " AND c.category_id = ?";
    }
    $sql .= " ORDER BY c.uploaded_at DESC";

    $stmt = $mysqli->prepare($sql);
    $likeQuery = "%" . $query . "%";

    if (!empty($categoryId)) {
        $stmt->bind_param("ssi", $likeQuery, $likeQuery, $categoryId);
    } else {
        $stmt->bind_param("ss", $likeQuery, $likeQuery);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    
    $contents = [];
    while ($row = $result->fetch_assoc()) {
        $contents[] = $row;
    }
    $stmt->close();
    $mysqli->close();
    
    return $contents;
}

function getHighlightedContents() {

    $mysqli = getDB();

    $sql = "SELECT c.*, cat.name AS category_name
            FROM contents c
            JOIN categories cat
            ON c.category_id = cat.id
            ORDER BY c.download_count DESC
            LIMIT 5";

    $result = $mysqli->query($sql);

    $contents = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }
    }

    $mysqli->close();

    return $contents;
}
function getContentsByCategory($id) {

    $mysqli = getDB();

    $sql = "SELECT c.*, cat.name AS category_name
            FROM contents c
            JOIN categories cat ON c.category_id = cat.id
            WHERE c.category_id = ?
            OR c.category_id IN (
                SELECT id FROM categories WHERE parent_id = ?
            )";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param('ii', $id, $id);

    $stmt->execute();

    $result = $stmt->get_result();

    $contents = [];

    while ($row = $result->fetch_assoc()) {
        $contents[] = $row;
    }

    $stmt->close();
    $mysqli->close();

    return $contents;
}
?>