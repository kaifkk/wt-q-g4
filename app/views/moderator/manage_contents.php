<?php
require_once __DIR__ . '/../../core/moderatorGate.php';
require_once __DIR__ . '/../../models/moderatorModel.php';
require_once __DIR__ . '/../../models/contentModel.php';
require_once __DIR__ . '/../../config/config.php';

$search     = trim($_GET['search']   ?? '');
$categoryId = (int)($_GET['category'] ?? 0);

$contents   = modGetAllContents($search, $categoryId > 0 ? $categoryId : '');
$categories = getAllCategories();

$success = $_SESSION['mod_content_success'] ?? '';
$errors  = $_SESSION['mod_content_errors']  ?? [];
unset($_SESSION['mod_content_success'], $_SESSION['mod_content_errors']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Contents – MediaFTP Moderator</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v=<?= $version ?>">
</head>
<body>
    <div class="navbar">
        <div class="mediaFTPnavbar">
            <img src="../../public/assets/icons/media.png" alt="mediaLogo">
            <h2>MediaFTP</h2>
        </div>
        <div class="navbarLoginRegisterButtonAuthenticationForm">
            <input type="button" value="Dashboard"       onclick="window.location.href='dashboard.php'" />
            <input type="button" value="Upload Content"  onclick="window.location.href='upload_content.php'" />
            <input type="button" value="Requests"        onclick="window.location.href='view_requests.php'" />
            <input type="button" value="Logout"          onclick="window.location.href='../../controllers/logoutController.php'" />
        </div>
    </div>

    <div class="mod-wrapper">
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

            <form method="GET" action="">
                <div class="filter-bar">
                    <input type="text" name="search"
                           placeholder="Search by title or description…"
                           value="<?= htmlspecialchars($search) ?>">
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $categoryId === (int)$cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-filter">🔍 Filter</button>
                    <?php if ($search !== '' || $categoryId > 0): ?>
                        <a href="manage_contents.php" class="btn-clear">✕ Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <table id="contentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Uploader</th>
                        <th>Downloads</th>
                        <th>Uploaded</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contents)): ?>
                        <tr class="empty-row"><td colspan="7">No contents found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($contents as $i => $c): ?>
                            <tr id="row-<?= $c['id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td class="truncate" title="<?= htmlspecialchars($c['title']) ?>">
                                    <?= htmlspecialchars($c['title']) ?>
                                </td>
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
                                <td>
                                    <button class="btn-danger"
                                            onclick="askDelete(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['title'])) ?>', this)">
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

    <div id="confirmOverlay">
        <div id="confirmBox">
            <p id="confirmMsg">Delete this content?</p>
            <div class="confirm-btns">
                <button class="btn-confirm-yes" id="confirmYes">Yes, Delete</button>
                <button class="btn-confirm-no"  id="confirmNo">Cancel</button>
            </div>
        </div>
    </div>

    <div id="toast"></div>
    
    <script src="../../public/assets/js/moderator/script.js"></script> 
</body>
</html>