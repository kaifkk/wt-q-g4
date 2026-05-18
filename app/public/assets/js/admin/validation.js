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