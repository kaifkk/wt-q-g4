<?php

session_start();
function checkLogin() {
    if(!isset($_SESSION['user_id'])) {
        header('Location: app/views/login.php');
        exit();
    }
}

?>