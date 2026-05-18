<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../config/config.php';

$stats = getAdminDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – MediaFTP</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v=<?= $version ?>">
</head>
<body>
    <div class="navbar">
        <div class="mediaFTPnavbar">
            <img src="../../public/assets/icons/media.png" alt="mediaLogo">
            <h2>MediaFTP</h2>
        </div>
        <div class="navbarLoginRegisterButtonAuthenticationForm">
            <input type="button" value="Home"    onclick="window.location.href='../../index.php'" />
            <input type="button" value="Profile" onclick="window.location.href='../profile.php'" />
            <input type="button" value="Logout"  onclick="window.location.href='../../controllers/logoutController.php'" />
        </div>
    </div>

    <div class="admin-wrapper">
        <h1>Admin Dashboard</h1>
        <p style="color:#aaa; margin-top:-10px; margin-bottom:20px;">
            Welcome back, <strong style="color:white;"><?= htmlspecialchars($_SESSION['name']) ?></strong>
        </p>

        <div class="stats-grid">
            <div class="stat-card">
                <h2><?= $stats['total_contents'] ?></h2>
                <p>Total Contents</p>
            </div>
            <div class="stat-card">
                <h2><?= $stats['total_categories'] ?></h2>
                <p>Categories</p>
            </div>
            <div class="stat-card">
                <h2><?= $stats['total_moderators'] ?></h2>
                <p>Moderators</p>
            </div>
            <div class="stat-card">
                <h2><?= $stats['pending_requests'] ?></h2>
                <p>Pending Requests</p>
            </div>
        </div>

        <div class="admin-nav">
            <a href="manage_moderators.php">👤 Manage Moderators</a>
            <a href="manage_contents.php">📁 Manage Contents</a>
            <a href="upload_content.php">⬆️ Upload Content</a>
        </div>
    </div>
</body>
</html>
