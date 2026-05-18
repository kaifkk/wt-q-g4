<?php

require_once __DIR__ . '/../core/database.php';

function getHighlightedContents() {

    global $conn;

    $sql = "SELECT c.*, cat.name AS category_name
            FROM contents c
            JOIN categories cat
            ON c.category_id = cat.id
            ORDER BY c.download_count DESC
            LIMIT 5";

    return mysqli_query($conn, $sql);
}

function getContentsByCategory($id) {

    global $conn;

    $sql = "SELECT * FROM contents
            WHERE category_id=?
            OR category_id IN (
                SELECT id FROM categories
                WHERE parent_id=?
            )";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, 'ii', $id, $id);

    mysqli_stmt_execute($stmt);

    return mysqli_stmt_get_result($stmt);
}
?>