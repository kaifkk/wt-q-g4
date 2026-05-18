<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../models/userModel.php';
require '../config/config.php';

$user = findUserById((int) $_SESSION['user_id']);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$profileErrors  = $_SESSION['profile_errors']  ?? [];
$profileSuccess = $_SESSION['profile_success'] ?? '';
$passwordErrors = $_SESSION['password_errors'] ?? [];
$passwordSuccess= $_SESSION['password_success']?? '';
unset(
    $_SESSION['profile_errors'],
    $_SESSION['profile_success'],
    $_SESSION['password_errors'],
    $_SESSION['password_success']
);

$avatarSrc = !empty($user['profile_picture'])
    ? '../public/assets/uploads/' . htmlspecialchars($user['profile_picture'])
    : '../public/assets/icons/media.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile – MediaFTP</title>
    <link rel="stylesheet" href="../public/assets/css/style.css?v=<?=$version?>">
</head>
<body>
    <div class="navbar">
        <div class="mediaFTPnavbar">
            <img src="../public/assets/icons/media.png" alt="mediaLogo">
            <h2>MediaFTP</h2>
        </div>
        <div class="navbarLoginRegisterButtonAuthenticationForm">
            <input type="button" value="Home" onclick="window.location.href='../index.php'" />
            <input type="button" value="Dashboard" onclick="window.location.href='dashboard.php'" />
        </div>
    </div>

    <div class="profile-wrapper">
        <div class="profile-header">
            <img src="<?= $avatarSrc ?>" alt="Avatar" class="profile-avatar" id="headerAvatar">
            <div class="profile-header-info">
                <h2><?= htmlspecialchars($user['name']) ?></h2>
                <span><?= htmlspecialchars($user['role']) ?></span>
            </div>
        </div>

        <div class="section-card">
            <h3>Update Profile</h3>

            <?php if (!empty($profileSuccess)): ?>
                <div class="flash-success"><?= htmlspecialchars($profileSuccess) ?></div>
            <?php endif; ?>

            <?php if (!empty($profileErrors)): ?>
                <div class="flash-error">
                    <ul>
                        <?php foreach ($profileErrors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="../controllers/profileController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name"
                           value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Profile Picture</label>
                    <div class="avatar-preview-wrap">
                        <img src="<?= $avatarSrc ?>" alt="Preview" id="avatarPreview">
                        <input type="file" name="profile_picture" id="pictureInput"
                               accept="image/jpeg,image/png,image/gif,image/webp"
                               onchange="previewAvatar(event)">
                    </div>
                    <small style="color:#888;">JPEG, PNG, GIF or WebP — max 2 MB. Leave empty to keep current.</small>
                </div>

                <input type="submit" class="btn-primary" value="Save Changes">
            </form>
        </div>

        <div class="section-card">
            <h3>Change Password</h3>

            <?php if (!empty($passwordSuccess)): ?>
                <div class="flash-success"><?= htmlspecialchars($passwordSuccess) ?></div>
            <?php endif; ?>

            <?php if (!empty($passwordErrors)): ?>
                <div class="flash-error">
                    <ul>
                        <?php foreach ($passwordErrors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="../controllers/profileController.php" method="POST">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                           placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password"
                           placeholder="Minimum 8 characters" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Repeat new password" required>
                </div>

                <input type="submit" class="btn-primary" value="Change Password"
                       onclick="return validatePasswordForm()">
            </form>
        </div>

        <div class="section-card" style="text-align:center;">
            <h3>Session</h3>
            <p style="margin-bottom:16px; color:#aaa; font-size:0.9rem;">
                Logged in as <strong style="color:white;"><?= htmlspecialchars($user['email']) ?></strong>
            </p>
            <a href="../controllers/logoutController.php" class="btn-logout"
               onclick="return confirm('Log out of MediaFTP?')">Log Out</a>
        </div>

    </div>

    <script>
        function previewAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;
            const url = URL.createObjectURL(file);
            document.getElementById('avatarPreview').src = url;
            document.getElementById('headerAvatar').src  = url;
        }

        function validatePasswordForm() {
            const np = document.getElementById('new_password').value;
            const cp = document.getElementById('confirm_password').value;
            if (np.length < 8) {
                alert('New password must be at least 8 characters.');
                return false;
            }
            if (np !== cp) {
                alert('New passwords do not match.');
                return false;
            }
            return true;
        }
    </script>

</body>
</html>