<?php
function getDB() {
    $host = '127.0.0.1';
    $db   = 'mediahub'; 
    $user = 'root';
    $pass = ''; 
    
    $mysqli = new mysqli($host, $user, $pass, $db);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    return $mysqli;
}
?>