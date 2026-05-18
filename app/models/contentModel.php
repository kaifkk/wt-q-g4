<?php
require_once __DIR__ . '/../core/database.php';


function getAllCategories() {
    $mysqli = getDB();
    $sql = "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC";
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

function getSubCategories($parentId) {
    $mysqli = getDB();
    $sql = "SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $parentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subCats = [];
    while ($row = $result->fetch_assoc()) {
        $subCats[] = $row;
    }
    $stmt->close();
    $mysqli->close();
    return $subCats;
}


function searchContents($query, $categoryId = '', $subCategoryId = '') {
    $mysqli = getDB();
    $sql = "SELECT c.*, cat.name as category_name FROM contents c 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE (c.title LIKE ? OR c.description LIKE ?)";
            
    $types = "ss";
    $params = ["%$query%", "%$query%"];


    if (!empty($subCategoryId)) {
        $sql .= " AND c.category_id = ?";
        $types .= "s";
        $params[] = $subCategoryId;
    } 

    else if (!empty($categoryId)) {
        $sql .= " AND (c.category_id = ? OR c.category_id IN (SELECT id FROM categories WHERE parent_id = ?))";
        $types .= "ss";
        $params[] = $categoryId;
        $params[] = $categoryId;
    }

    $sql .= " ORDER BY c.uploaded_at DESC";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params); 
    
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