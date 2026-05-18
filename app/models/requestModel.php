<?php
require_once __DIR__ . '/../core/database.php';

function createContentRequest($ip, $title, $category, $message) {
    $mysqli = getDB();
    $sql = "INSERT INTO content_requests (requester_ip, content_title, category_requested, message, status) VALUES (?, ?, ?, ?, 'pending')";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $ip, $title, $category, $message);
    $success = $stmt->execute(); 
    
    $stmt->close();
    $mysqli->close();
    
    return $success;
}
?>