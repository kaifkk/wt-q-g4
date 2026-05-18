<?php
session_start();

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
</head>
<body>
    <div>
        <div class="navbar">
            <div class="mediaFTPnavbar">
                <img src="../public/assets/icons/media.png" alt="mediaLogo">
                <h2>MediaFTP</h2>
            </div>
            <div class="navbarLoginRegisterButtonAuthenticationForm">
                <input type="button" value="Login" onclick="window.location.href='login.php'">
            </div>
        </div>

        <div class="registrationForm">
            <div class="registrationFormCard">
                <form action="../controllers/registrationController.php" method="post" class="registrationFormInputs" onSubmit="return validateRegister()">
                    <h1>Create Account</h1>
                    <p>Full name</p>
                    <input type="text" name="name" placeholder="Name" class="grayInputFields" />
                    <span>
                        <?php echo $errors['name'] ?? ''; ?>
                    </span>
                    <p>Email</p>
                    <input type="email" name="email" id="email" placeholder="Email" onkeyup="checkEmail()" />
                    <div id="emailMessage"></div>
                    <span>
                        <?php echo $errors['email'] ?? ''; ?>
                    </span>

                    <p>Role</p>
                    <select name="role">
                        <option value="admin">Admin</option>
                        <option value="moderator">Moderator</option>
                    </select>

                    <p>Password(min 8 chars)</p>
                    <input type="password" name="password" placeholder="Password" id="password" />
                    <span>
                        <?php echo $errors['password'] ?? ''; ?>
                    </span>

                    <p>Confirm password</p>
                    <input type="password" name="confirm_password" placeholder="Confirm password" id="confirm_password" />
                    <br><br>
                    <input type="submit" name="registerBtn" value="Register" />
                </form>
            </div>
        </div>
    </div>
    <script src="../public/assets/js/validation.js"></script>
</body>
</html>