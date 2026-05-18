function confirmDelete(id, name) {
    if (confirm('Delete moderator "' + name + '"?\nTheir uploaded contents will be reassigned to admin.')) {
        document.getElementById('deleteModId').value = id;
        document.getElementById('deleteModForm').submit();
    }
}

function validateModForm(e) {
    let valid = true;

    const name     = document.getElementById('mod_name');
    const email    = document.getElementById('mod_email');
    const password = document.getElementById('mod_password');
    const confirm  = document.getElementById('mod_confirm');

    document.getElementById('err-name').textContent     = '';
    document.getElementById('err-email').textContent    = '';
    document.getElementById('err-password').textContent = '';
    document.getElementById('err-confirm').textContent  = '';

    if (name.value.trim() === '') {
        document.getElementById('err-name').textContent = 'Full name is required.';
        valid = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value.trim())) {
        document.getElementById('err-email').textContent = 'Enter a valid email address.';
        valid = false;
    }

    if (password.value.length < 8) {
        document.getElementById('err-password').textContent = 'Password must be at least 8 characters.';
        valid = false;
    }

    if (password.value !== confirm.value) {
        document.getElementById('err-confirm').textContent = 'Passwords do not match.';
        valid = false;
    }

    if (!valid) e.preventDefault();
    return valid;
}

const ALLOWED_EXTS  = ['zip','rar','7z','tar','gz','mp4','mkv','avi','mov','mp3','flac','wav',
                        'exe','msi','iso','apk','pdf','txt'];
const MAX_BYTES     = 200 * 1024 * 1024; // 200 MB

function showFileInfo(input) {
    const info = document.getElementById('fileInfo');
    if (input.files.length === 0) { info.textContent = ''; return; }
    const f = input.files[0];
    const mb = (f.size / 1024 / 1024).toFixed(2);
    info.textContent = `Selected: ${f.name} (${mb} MB)`;
}

function validateUploadForm(e) {
    let valid = true;

    document.getElementById('err-title').textContent    = '';
    document.getElementById('err-category').textContent = '';
    document.getElementById('err-file').textContent     = '';

    const title    = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;
    const fileInput= document.getElementById('content_file');

    if (title === '') {
        document.getElementById('err-title').textContent = 'Title is required.';
        valid = false;
    }
    if (category === '') {
        document.getElementById('err-category').textContent = 'Please select a category.';
        valid = false;
    }
    if (fileInput.files.length === 0) {
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

function confirmDeleteContent(id, title) {
    if (confirm('Delete content "' + title + '"?\nThe file will also be removed from the server.')) {
        document.getElementById('deleteContentId').value = id;
        document.getElementById('deleteContentForm').submit();
    }
}

const ALLOWED_EXTS = ['zip','rar','7z','tar','gz','mp4','mkv','avi','mov','mp3','flac','wav',
                        'exe','msi','iso','apk','pdf','txt'];
const MAX_BYTES    = 200 * 1024 * 1024;

function validateEditForm(e) {
    let valid = true;

    document.getElementById('err-title').textContent    = '';
    document.getElementById('err-category').textContent = '';
    document.getElementById('err-file').textContent     = '';

    const title    = document.getElementById('title').value.trim();
    const category = document.getElementById('category_id').value;
    const fileInput= document.getElementById('content_file');

    if (title === '') {
        document.getElementById('err-title').textContent = 'Title is required.';
        valid = false;
    }
    if (category === '') {
        document.getElementById('err-category').textContent = 'Please select a category.';
        valid = false;
    }
    if (fileInput.files.length > 0) {
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