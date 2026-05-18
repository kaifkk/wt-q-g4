<?php
require_once __DIR__ . '/../core/database.php';

function findUserByEmail($email) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    return $user;
}

function findUserById($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $mysqli->close();
    return $user;
}

function createUser($name, $email, $password, $role) {
    $mysqli = getDB();
    $hash   = password_hash($password, PASSWORD_DEFAULT);
    $stmt   = $mysqli->prepare(
        "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $success;
}

function saveRememberToken($id, $token) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
    $stmt->bind_param('si', $token, $id);
    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $success;
}

function clearRememberToken($id) {
    $mysqli = getDB();
    $stmt   = $mysqli->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->bind_param('i', $id);
    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $success;
}


function updateProfile($id, $name, $email, $picturePath = null) {
    $mysqli = getDB();

    if ($picturePath !== null) {
        $stmt = $mysqli->prepare(
            "UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?"
        );
        $stmt->bind_param('sssi', $name, $email, $picturePath, $id);
    } else {
        $stmt = $mysqli->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );
        $stmt->bind_param('ssi', $name, $email, $id);
    }

    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();
    return $success;
}

function updatePassword($id, $currentPassword, $newPassword) {
    $user = findUserById($id);

    if (!$user) {
        return 'User not found.';
    }
    if (!password_verify($currentPassword, $user['password_hash'])) {
        return 'Current password is incorrect.';
    }

    $mysqli  = getDB();
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt    = $mysqli->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param('si', $newHash, $id);
    $success = $stmt->execute();
    $stmt->close();
    $mysqli->close();

    return $success ? true : 'Failed to update password.';
}
?>