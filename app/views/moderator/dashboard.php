<?php
require_once __DIR__ . '/../../core/moderatorGate.php';
require_once __DIR__ . '/../../models/moderatorModel.php';
require_once __DIR__ . '/../../config/config.php';

$stats = modGetDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard – MediaFTP</title>
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

    <div class="mod-wrapper">
        <h1>Moderator Dashboard</h1>
        <p style="color:#aaa; margin-top:-10px; margin-bottom:20px;">
            Welcome back, <strong style="color:white;"><?= htmlspecialchars($_SESSION['name']) ?></strong>
            &nbsp;<span style="background:#1a3d6b;color:#4da6ff;border-radius:4px;padding:2px 8px;font-size:0.8rem;">Moderator</span>
        </p>

        <div class="stats-grid">
            <div class="stat-card">
                <h2><?= $stats['total_contents'] ?></h2>
                <p>Total Contents</p>
            </div>
            <div class="stat-card pending">
                <h2><?= $stats['pending_requests'] ?></h2>
                <p>Pending Requests</p>
            </div>
            <div class="stat-card fulfilled">
                <h2><?= $stats['fulfilled_requests'] ?></h2>
                <p>Fulfilled Requests</p>
            </div>
            <div class="stat-card rejected">
                <h2><?= $stats['rejected_requests'] ?></h2>
                <p>Rejected Requests</p>
            </div>
        </div>

        <div class="mod-nav">
            <a href="manage_contents.php">📁 Manage Contents</a>
            <a href="upload_content.php">⬆️ Upload Content</a>
            <a href="view_requests.php">
                📬 Content Requests
                <?php if ($stats['pending_requests'] > 0): ?>
                    <span class="mod-badge"><?= $stats['pending_requests'] ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
</body>
</html>