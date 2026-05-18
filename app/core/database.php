<?php

function getDB() {
    $conn = mysqli_connect(
        'localhost',
        'root',
        '',
        'mediahub'
    );

    if(!$conn) {
        die('Database Connection Failed: ' . mysqli_connect_error());
    }

    return $conn;
}