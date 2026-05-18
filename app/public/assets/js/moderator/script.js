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