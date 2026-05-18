<?php
require_once __DIR__ . '/../../core/adminGate.php';
require_once __DIR__ . '/../../models/adminModel.php';
require_once __DIR__ . '/../../models/userModel.php';
require_once __DIR__ . '/../../config/config.php';

$moderators = getAllModerators();

$errors  = $_SESSION['mod_errors']  ?? [];
$success = $_SESSION['mod_success'] ?? '';
unset($_SESSION['mod_errors'], $_SESSION['mod_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Moderators – MediaFTP</title>
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
        <h1 style="margin-bottom:24px;">Manage Moderators</h1>

        <?php if ($success): ?>
            <div class="flash-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="flash-error">
                <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <div class="section-card">
            <h3>Add New Moderator</h3>
            <form action="../../controllers/admin/moderatorController.php" method="POST"
                  onsubmit="return validateModForm(event)">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label for="mod_name">Full Name *</label>
                    <input type="text" id="mod_name" name="name" placeholder="Enter full name">
                    <span class="err-msg" id="err-name"></span>
                </div>
                <div class="form-group">
                    <label for="mod_email">Email Address *</label>
                    <input type="email" id="mod_email" name="email" placeholder="Enter email">
                    <span class="err-msg" id="err-email"></span>
                </div>
                <div class="form-group">
                    <label for="mod_password">Password * <small style="color:#666;">(min 8 characters)</small></label>
                    <input type="password" id="mod_password" name="password" placeholder="Enter password">
                    <span class="err-msg" id="err-password"></span>
                </div>
                <div class="form-group">
                    <label for="mod_confirm">Confirm Password *</label>
                    <input type="password" id="mod_confirm" name="confirm_password" placeholder="Repeat password">
                    <span class="err-msg" id="err-confirm"></span>
                </div>

                <button type="submit" class="btn-primary">Add Moderator</button>
            </form>
        </div>

        <div class="section-card">
            <h3>All Moderators (<?= count($moderators) ?>)</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($moderators)): ?>
                        <tr class="empty-row"><td colspan="5">No moderators found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($moderators as $i => $mod): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($mod['name']) ?></td>
                                <td><?= htmlspecialchars($mod['email']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($mod['created_at']))) ?></td>
                                <td>
                                    <button class="btn-danger"
                                            onclick="confirmDelete(<?= $mod['id'] ?>, '<?= htmlspecialchars(addslashes($mod['name'])) ?>')">
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

    <form id="deleteModForm" action="../../controllers/admin/moderatorController.php" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="mod_id" id="deleteModId">
    </form>

    <script src="../../public/assets/js/admin/validation.js"></script> 
</body>
</html>
