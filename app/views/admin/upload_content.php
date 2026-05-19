<?php
session_start();
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../config/config.php';

$grouped = getCategoriesGrouped();

$errors  = $_SESSION['content_errors']  ?? [];
$success = $_SESSION['content_success'] ?? '';
unset($_SESSION['content_errors'], $_SESSION['content_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Content – MediaFTP Admin</title>
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
            <input type="button" value="Manage Contents" onclick="window.location.href='manage_contents.php'" />
            <input type="button" value="Logout"          onclick="window.location.href='../../controllers/logoutController.php'" />
        </div>
    </div>

    <div class="admin-wrapper">
        <a class="back-link" href="manage_contents.php">← Back to Contents</a>
        <h1 style="margin-bottom:24px;">Upload New Content</h1>

        <?php if ($success): ?>
            <div class="flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="flash-error">
                <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <div class="section-card">
            <h3>Content Details</h3>
            <form id="uploadForm"
                  action="../../controllers/admin/contentController.php"
                  method="POST"
                  enctype="multipart/form-data"
                  onsubmit="return validateUploadForm(event)">
                <input type="hidden" name="action" value="upload">

                <div class="form-group">
                    <label for="title">
                        Title *
                        <span class="char-count"><span id="titleCount">0</span>/255</span>
                    </label>
                    <input type="text" id="title" name="title"
                           placeholder="Enter content title"
                           maxlength="255"
                           oninput="document.getElementById('titleCount').textContent=this.value.length">
                    <span class="err-msg" id="err-title"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                              placeholder="Optional description (max 1000 chars)"
                              maxlength="1000"></textarea>
                </div>

                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select id="category_id" name="category_id">
                        <option value="">— Select Category —</option>
                        <?php foreach ($grouped['parents'] as $parentId => $parent): ?>
                            <optgroup label="<?= htmlspecialchars($parent['name']) ?>">
                                <option value="<?= $parent['id'] ?>"><?= htmlspecialchars($parent['name']) ?></option>
                                <?php if (!empty($grouped['children'][$parentId])): ?>
                                    <?php foreach ($grouped['children'][$parentId] as $child): ?>
                                        <option value="<?= $child['id'] ?>">
                                            &nbsp;&nbsp;↳ <?= htmlspecialchars($child['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </optgroup>
                        <?php endforeach; ?>
                        <?php foreach ($grouped['children'] as $pid => $kids): ?>
                            <?php if (!isset($grouped['parents'][$pid])): ?>
                                <?php foreach ($kids as $child): ?>
                                    <option value="<?= $child['id'] ?>"><?= htmlspecialchars($child['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <span class="err-msg" id="err-category"></span>
                </div>

                <div class="form-group">
                    <label for="content_file">
                        File *
                        <small style="color:#666;">(max 200 MB – zip, rar, mp4, mkv, avi, mp3, exe, iso …)</small>
                    </label>
                    <input type="file" id="content_file" name="content_file"
                           onchange="showFileInfo(this)">
                    <div id="fileInfo"></div>
                    <span class="err-msg" id="err-file"></span>
                </div>

                <button type="submit" class="btn-primary">⬆️ Upload Content</button>
            </form>
        </div>
    </div>

    <script>
        const ALLOWED_EXTS = ['zip','rar','7z','tar','gz','mp4','mkv','avi','mov',
                              'mp3','flac','wav','exe','msi','iso','apk','pdf','txt'];
        const MAX_BYTES    = 200 * 1024 * 1024;

        function showFileInfo(input) {
            const info = document.getElementById('fileInfo');
            if (!input.files.length) { info.textContent = ''; return; }
            const f  = input.files[0];
            const mb = (f.size / 1024 / 1024).toFixed(2);
            const ok = mb <= 200;
            info.style.color = ok ? '#888' : '#e05c5c';
            info.textContent = `Selected: ${f.name} (${mb} MB)`;
        }

        function validateUploadForm(e) {
            let valid = true;

            ['err-title','err-category','err-file'].forEach(id => {
                document.getElementById(id).textContent = '';
            });

            const title     = document.getElementById('title').value.trim();
            const category  = document.getElementById('category_id').value;
            const fileInput = document.getElementById('content_file');

            if (title === '') {
                document.getElementById('err-title').textContent = 'Title is required.';
                valid = false;
            } else if (title.length > 255) {
                document.getElementById('err-title').textContent = 'Title must be 255 characters or fewer.';
                valid = false;
            }

            if (category === '') {
                document.getElementById('err-category').textContent = 'Please select a category.';
                valid = false;
            }

            if (!fileInput.files.length) {
                document.getElementById('err-file').textContent = 'Please select a file to upload.';
                valid = false;
            } else {
                const f   = fileInput.files[0];
                const ext = f.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXTS.includes(ext)) {
                    document.getElementById('err-file').textContent = 'File type .' + ext + ' is not allowed.';
                    valid = false;
                } else if (f.size > MAX_BYTES) {
                    document.getElementById('err-file').textContent = 'File exceeds the 200 MB limit.';
                    valid = false;
                }
            }

            if (!valid) e.preventDefault();
            return valid;
        }
    </script>
</body>
</html>
