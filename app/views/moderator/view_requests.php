<?php
require_once __DIR__ . '/../../core/moderatorGate.php';
require_once __DIR__ . '/../../models/moderatorModel.php';
require_once __DIR__ . '/../../config/config.php';

$statusFilter = trim($_GET['status'] ?? '');
$requests     = modGetAllRequests($statusFilter !== '' ? $statusFilter : '');
$pendingCount = modCountPendingRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Requests – MediaFTP Moderator</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css?v=<?= $version ?>">
    <style>
        .mod-wrapper { max-width: 1150px; margin: 30px auto; padding: 0 20px; }

        .section-card {
            background-color: #262624;
            border: 1px solid #41413E;
            border-radius: 8px;
            padding: 24px 28px;
            margin-bottom: 28px;
        }
        .section-card h3 { margin-top: 0; color: white; }

        table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #41413E; }
        th { color: #aaa; font-weight: normal; }
        td { color: #faf9f5b1; vertical-align: top; }

        .back-link { color: #4da6ff; text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { text-decoration: underline; }

        .empty-row td { text-align: center; color: #666; padding: 20px; }
        .truncate { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .msg-cell { max-width: 260px; white-space: pre-wrap; word-break: break-word; }

        .badge-pending   { display:inline-block; background:#3d2e00; color:#f0a500; border-radius:12px; padding:3px 10px; font-size:0.78rem; }
        .badge-fulfilled { display:inline-block; background:#1a3d1a; color:#6fcf6f; border-radius:12px; padding:3px 10px; font-size:0.78rem; }
        .badge-rejected  { display:inline-block; background:#3d1a1a; color:#cf6f6f; border-radius:12px; padding:3px 10px; font-size:0.78rem; }

        .btn-fulfill {
            background: #1a5c1a; color: #faf9f5b1;
            border: none; padding: 5px 12px;
            border-radius: 4px; cursor: pointer; font-size: 0.8rem;
            transition: background 0.2s; margin-right: 4px;
        }
        .btn-fulfill:hover { background: #246e24; }
        .btn-fulfill:disabled { background: #444; cursor: not-allowed; opacity: 0.6; }

        .btn-reject {
            background: #7a1f1f; color: #faf9f5b1;
            border: none; padding: 5px 12px;
            border-radius: 4px; cursor: pointer; font-size: 0.8rem;
            transition: background 0.2s;
        }
        .btn-reject:hover { background: #a32828; }
        .btn-reject:disabled { background: #444; cursor: not-allowed; opacity: 0.6; }

        .btn-pending {
            background: #3d2e00; color: #f0a500;
            border: none; padding: 5px 12px;
            border-radius: 4px; cursor: pointer; font-size: 0.8rem;
            transition: background 0.2s;
        }
        .btn-pending:hover { background: #5a4400; }
        .btn-pending:disabled { background: #444; cursor: not-allowed; opacity: 0.6; }

        .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; align-items: center; }
        .filter-bar select {
            background: #30302E; color: white;
            border: 1px solid #41413E; border-radius: 4px;
            padding: 8px 12px; outline: none; font-size: 0.88rem;
        }
        .btn-filter {
            background: #0d447b; color: #faf9f5b1;
            border: none; padding: 8px 18px;
            border-radius: 4px; cursor: pointer; font-size: 0.88rem;
        }
        .btn-filter:hover { background: #1455a0; }
        .btn-clear {
            background: #333; color: #aaa;
            border: none; padding: 8px 14px;
            border-radius: 4px; cursor: pointer; font-size: 0.88rem;
            text-decoration: none;
        }
        .btn-clear:hover { background: #444; }

        .summary-pills { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
        .pill {
            background: #262624; border: 1px solid #41413E;
            border-radius: 20px; padding: 6px 16px;
            font-size: 0.82rem; color: #aaa;
        }
        .pill strong { color: white; }
        .pill.pending  strong { color: #f0a500; }
        .pill.fulfilled strong { color: #6fcf6f; }
        .pill.rejected  strong { color: #cf6f6f; }

        #toast {
            position: fixed; bottom: 24px; right: 24px;
            padding: 12px 20px; border-radius: 6px;
            font-size: 0.9rem; z-index: 9999;
            opacity: 0; transform: translateY(10px);
            transition: opacity 0.3s, transform 0.3s;
            pointer-events: none;
        }
        #toast.show { opacity: 1; transform: translateY(0); }
        #toast.toast-success { background: #1a3d1a; border: 1px solid #2e6b2e; color: #6fcf6f; }
        #toast.toast-error   { background: #3d1a1a; border: 1px solid #6b2e2e; color: #cf6f6f; }

        tr.updating { opacity: 0.4; transition: opacity 0.3s; }
    </style>
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
            <input type="button" value="Upload Content"  onclick="window.location.href='upload_content.php'" />
            <input type="button" value="Logout"          onclick="window.location.href='../../controllers/logoutController.php'" />
        </div>
    </div>

    <div class="mod-wrapper">
        <a class="back-link" href="dashboard.php">← Back to Dashboard</a>
        <h1 style="margin-bottom: 8px;">Content Requests</h1>
        <p style="color:#aaa; margin-bottom:20px; font-size:0.9rem;">
            Review member requests and mark them as fulfilled or rejected.
            <?php if ($pendingCount > 0): ?>
                <span style="background:#3d2e00;color:#f0a500;border-radius:4px;padding:2px 8px;font-size:0.8rem;margin-left:6px;">
                    <?= $pendingCount ?> pending
                </span>
            <?php endif; ?>
        </p>

        <?php
        $all   = modGetAllRequests('');
        $total = count($all);
        $pend  = count(array_filter($all, fn($r) => $r['status'] === 'pending'));
        $fulf  = count(array_filter($all, fn($r) => $r['status'] === 'fulfilled'));
        $rej   = count(array_filter($all, fn($r) => $r['status'] === 'rejected'));
        ?>
        <div class="summary-pills">
            <div class="pill">Total: <strong><?= $total ?></strong></div>
            <div class="pill pending">Pending: <strong><?= $pend ?></strong></div>
            <div class="pill fulfilled">Fulfilled: <strong><?= $fulf ?></strong></div>
            <div class="pill rejected">Rejected: <strong><?= $rej ?></strong></div>
        </div>

        <div class="section-card">
            <h3>
                <?php if ($statusFilter !== ''): ?>
                    <?= ucfirst(htmlspecialchars($statusFilter)) ?> Requests (<?= count($requests) ?>)
                <?php else: ?>
                    All Requests (<?= count($requests) ?>)
                <?php endif; ?>
            </h3>

            <form method="GET" action="">
                <div class="filter-bar">
                    <select name="status">
                        <option value="">All Statuses</option>
                        <option value="pending"   <?= $statusFilter === 'pending'   ? 'selected' : '' ?>>Pending</option>
                        <option value="fulfilled" <?= $statusFilter === 'fulfilled' ? 'selected' : '' ?>>Fulfilled</option>
                        <option value="rejected"  <?= $statusFilter === 'rejected'  ? 'selected' : '' ?>>Rejected</option>
                    </select>
                    <button type="submit" class="btn-filter">🔍 Filter</button>
                    <?php if ($statusFilter !== ''): ?>
                        <a href="view_requests.php" class="btn-clear">✕ Clear</a>
                    <?php endif; ?>
                </div>
            </form>

            <table id="requestsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Requested Title</th>
                        <th>Category</th>
                        <th>Message</th>
                        <th>Requester IP</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                        <tr class="empty-row">
                            <td colspan="8">No requests found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($requests as $i => $req): ?>
                            <tr id="req-row-<?= $req['id'] ?>">
                                <td><?= $i + 1 ?></td>
                                <td class="truncate" title="<?= htmlspecialchars($req['content_title']) ?>">
                                    <?= htmlspecialchars($req['content_title']) ?>
                                </td>
                                <td>
                                    <?php if (!empty($req['category_requested'])): ?>
                                        <span style="color:#4da6ff;"><?= htmlspecialchars($req['category_requested']) ?></span>
                                    <?php else: ?>
                                        <span style="color:#666;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="msg-cell">
                                    <?php if (!empty($req['message'])): ?>
                                        <?= htmlspecialchars(mb_strimwidth($req['message'], 0, 120, '…')) ?>
                                    <?php else: ?>
                                        <span style="color:#666;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size:0.8rem;color:#888;"><?= htmlspecialchars($req['requester_ip'] ?? '—') ?></td>
                                <td style="white-space:nowrap;"><?= htmlspecialchars(date('d M Y', strtotime($req['created_at']))) ?></td>
                                <td id="req-status-<?= $req['id'] ?>">
                                    <?php
                                    $cls = match($req['status']) {
                                        'fulfilled' => 'badge-fulfilled',
                                        'rejected'  => 'badge-rejected',
                                        default     => 'badge-pending',
                                    };
                                    ?>
                                    <span class="<?= $cls ?>"><?= ucfirst($req['status']) ?></span>
                                </td>
                                <td id="req-actions-<?= $req['id'] ?>" style="white-space:nowrap;">
                                    <?php if ($req['status'] !== 'fulfilled'): ?>
                                        <button class="btn-fulfill"
                                                onclick="updateStatus(<?= $req['id'] ?>, 'fulfilled', this)">
                                            ✓ Fulfill
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($req['status'] !== 'rejected'): ?>
                                        <button class="btn-reject"
                                                onclick="updateStatus(<?= $req['id'] ?>, 'rejected', this)">
                                            ✗ Reject
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($req['status'] !== 'pending'): ?>
                                        <button class="btn-pending"
                                                onclick="updateStatus(<?= $req['id'] ?>, 'pending', this)">
                                            ↺ Pending
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="toast"></div>

    <script>
        function updateStatus(requestId, newStatus, clickedBtn) {
            const row         = document.getElementById('req-row-'     + requestId);
            const statusCell  = document.getElementById('req-status-'  + requestId);
            const actionsCell = document.getElementById('req-actions-' + requestId);

            const btns = actionsCell.querySelectorAll('button');
            btns.forEach(b => b.disabled = true);
            if (row) row.classList.add('updating');

            const formData = new FormData();
            formData.append('request_id', requestId);
            formData.append('status',     newStatus);

            fetch('../../controllers/moderator/requestController.php', {
                method: 'POST',
                body:   formData
            })
            .then(r => r.json())
            .then(data => {
                if (row) row.classList.remove('updating');

                if (data.success) {
                    const badgeClass = {
                        pending:   'badge-pending',
                        fulfilled: 'badge-fulfilled',
                        rejected:  'badge-rejected'
                    }[data.new_status] || 'badge-pending';

                    statusCell.innerHTML =
                        '<span class="' + badgeClass + '">' +
                        capitalize(data.new_status) + '</span>';

                    let buttonsHtml = '';
                    if (data.new_status !== 'fulfilled') {
                        buttonsHtml += '<button class="btn-fulfill" onclick="updateStatus(' + requestId + ', \'fulfilled\', this)">✓ Fulfill</button> ';
                    }
                    if (data.new_status !== 'rejected') {
                        buttonsHtml += '<button class="btn-reject" onclick="updateStatus(' + requestId + ', \'rejected\', this)">✗ Reject</button> ';
                    }
                    if (data.new_status !== 'pending') {
                        buttonsHtml += '<button class="btn-pending" onclick="updateStatus(' + requestId + ', \'pending\', this)">↺ Pending</button>';
                    }
                    actionsCell.innerHTML = buttonsHtml;

                    showToast(data.message, 'success');
                } else {
                    btns.forEach(b => b.disabled = false);
                    showToast(data.message, 'error');
                }
            })
            .catch(() => {
                if (row) row.classList.remove('updating');
                btns.forEach(b => b.disabled = false);
                showToast('Network error — please try again.', 'error');
            });
        }

        function showToast(msg, type) {
            const t = document.getElementById('toast');
            t.textContent = msg;
            t.className   = 'toast-' + type;
            t.classList.add('show');
            clearTimeout(t._timer);
            t._timer = setTimeout(() => t.classList.remove('show'), 3500);
        }

        function capitalize(s) {
            return s.charAt(0).toUpperCase() + s.slice(1);
        }
    </script>
</body>
</html>
