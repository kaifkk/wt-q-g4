<?php
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../config/config.php';

$contents = getAllContentsWithUploader();

$success = $_SESSION['content_success'] ?? '';
$errors  = $_SESSION['content_errors']  ?? [];
unset($_SESSION['content_success'], $_SESSION['content_errors']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contents - MediaFTP</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v=<?= $version ?>">
</head>
<body>
    <div class="navbar">
        <div class="mediaFTPnavbar">
            <img src="../../public/assets/icons/media.png" alt="mediaLogo">
            <h2>MediaFTP</h2>
        </div>
        <div class="navbarLoginRegisterButtonAuthenticationForm">
            <input type="button" value="Dashboard" onclick="window.location.href='dashboard.php'" />
            <input type="button" value="Logout"    onclick="window.location.href='../../controllers/logoutController.php'" />
        </div>
    </div>

    <div class="admin-wrapper">
        <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
        <h1 style="margin-bottom:20px;">Manage Contents</h1>

        <?php if ($success): ?>
            <div class="flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="flash-error">
                <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <a href="upload_content.php" class="btn-upload">⬆️ Upload New Content</a>

        <div class="section-card">
            <h3>All Contents (<?= count($contents) ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Uploader</th>
                        <th>Downloads</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contents)): ?>
                        <tr class="empty-row"><td colspan="7">No contents uploaded yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($contents as $i => $c): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td class="truncate"><?= htmlspecialchars($c['title']) ?></td>
                                <td>
                                    <?php if ($c['category_name']): ?>
                                        <span class="badge"><?= htmlspecialchars($c['category_name']) ?></span>
                                    <?php else: ?>
                                        <span style="color:#666;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $c['uploader_name'] ? htmlspecialchars($c['uploader_name']) : '<span style="color:#666;">Unassigned</span>' ?></td>
                                <td><?= (int)$c['download_count'] ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($c['uploaded_at']))) ?></td>
                                <td style="white-space:nowrap;">
                                    <a class="btn-edit" href="edit_content.php?id=<?= $c['id'] ?>">Edit</a>
                                    <button class="btn-danger" style="margin-left:6px;"
                                            onclick="confirmDeleteContent(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['title'])) ?>')">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <form id="deleteContentForm" action="../../controllers/admin/contentController.php" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="content_id" id="deleteContentId">
    </form>

    <script src="../../public/assets/js/admin/validation.js"></script> 
</body>
</html>
