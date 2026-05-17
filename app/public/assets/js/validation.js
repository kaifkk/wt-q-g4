function validateRegister() {

    let password =
        document.getElementById('password').value;

    let confirmPassword =
        document.getElementById('confirm_password').value;

    if(password.length < 8) {

        alert('Password minimum 8 characters');

        return false;
    }

    if(password !== confirmPassword) {

        alert('Passwords do not match');

        return false;
    }

    return true;
}

function checkEmail() {
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    let email = document.getElementById('email').value;

    if (!email.match(emailPattern)) {
        document.getElementById('emailMessage').innerHTML = "❌ Email pattern did not match";
        document.getElementById('emailMessage').style.color = "red";
        return false;
    }

    if(email.trim() == '') {
        document.getElementById('emailMessage').innerHTML = '';
        return;
    }
    fetch(
        '/ftpserver/app/controllers/check_email.php?email=' + email
    )
    .then(response => response.json())
    .then(data => {

        if(data.exists) {

            document.getElementById(
                'emailMessage'
            ).innerHTML =
                '<span style="color:red">Email Exists</span>';
        }
        else {

            document.getElementById(
                'emailMessage'
            ).innerHTML =
                '<span style="color:green">Email Available</span>';
        }
    });
}