<?php

$conn = mysqli_connect(
    'localhost',
    'root',
    '',
    'mediahub'
);

if(!$conn) {
    die('Database Connection Failed');
}
?>