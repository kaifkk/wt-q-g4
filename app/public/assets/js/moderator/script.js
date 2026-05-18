let pendingDeleteId   = null;
let pendingDeleteBtn  = null;
let pendingDeleteRow  = null;

function askDelete(id, title, btn) {
    pendingDeleteId  = id;
    pendingDeleteBtn = btn;
    pendingDeleteRow = document.getElementById('row-' + id);
    document.getElementById('confirmMsg').innerHTML =
        'Delete <strong>' + escapeHtml(title) + '</strong>?<br>' +
        '<span style="font-size:0.82rem;color:#aaa;">The file will also be removed from the server.</span>';
    document.getElementById('confirmOverlay').classList.add('open');
}

document.getElementById('confirmNo').addEventListener('click', () => {
    document.getElementById('confirmOverlay').classList.remove('open');
    pendingDeleteId = pendingDeleteBtn = pendingDeleteRow = null;
});

document.getElementById('confirmYes').addEventListener('click', () => {
    document.getElementById('confirmOverlay').classList.remove('open');
    if (!pendingDeleteId) return;

    const id  = pendingDeleteId;
    const btn = pendingDeleteBtn;
    const row = pendingDeleteRow;

    btn.disabled = true;
    if (row) row.classList.add('deleting');

    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('content_id', id);

    fetch('../../controllers/moderator/contentController.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (row) row.remove();
            showToast(data.message, 'success');
            const h3 = document.querySelector('.section-card h3');
            if (h3) {
                const tbody = document.querySelector('#contentsTable tbody');
                const rows  = tbody ? tbody.querySelectorAll('tr:not(.empty-row)').length : 0;
                h3.textContent = 'All Contents (' + rows + ')';
                if (rows === 0) {
                    tbody.innerHTML = '<tr class="empty-row"><td colspan="7">No contents found.</td></tr>';
                }
            }
        } else {
            if (row) row.classList.remove('deleting');
            btn.disabled = false;
            showToast(data.message, 'error');
        }
    })
    .catch(() => {
        if (row) row.classList.remove('deleting');
        btn.disabled = false;
        showToast('Network error — please try again.', 'error');
    });

    pendingDeleteId = pendingDeleteBtn = pendingDeleteRow = null;
});

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className   = 'toast-' + type;
    t.classList.add('show');
    clearTimeout(t._timer);
    t._timer = setTimeout(() => t.classList.remove('show'), 3500);
}

function escapeHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}