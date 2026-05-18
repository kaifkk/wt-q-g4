<?php
require '../core/database.php';

function findUserByEmail($email) {
    global $conn;

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return mysqli_fetch_assoc($result);
}

function createUser(
    $name,
    $email,
    $password,
    $role
) {
    global $conn;
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
            (name,email,password_hash,role)
            VALUES(?,?,?,?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        'ssss',
        $name,
        $email,
        $hash,
        $role
    );

    return mysqli_stmt_execute($stmt);

}

function saveRememberToken($id, $token) {

    global $conn;

    $sql = "UPDATE users
            SET remember_token=?
            WHERE id=?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        'si',
        $token,
        $id
    );

    return mysqli_stmt_execute($stmt);
}
?>