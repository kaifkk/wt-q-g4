<?php
session_start();

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
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
                <input type="button" value="Login">
                <input type="button" value="Register">
            </div>
        </div>

        <div class="registrationForm">
            <div class="registrationFormCard">
                <form action="../controllers/loginController.php" method="post" class="registrationFormInputs">
                    <p>Email</p>
                    <input type="email" name="email" id="email" placeholder="Email" onkeyup="checkEmail()" />
                    <div id="emailMessage"></div>

                    <p>Password</p>
                    <input type="password" name="password" placeholder="Password" id="password" />

                    <br>
                    <label>
                        <input type="checkbox" name="remember_me">
                        Remember Me
                    </label>

                    <br>
                    <input type="submit" name="loginBtn" value="Login" />
                </form>
                <span>
                    <?php echo $error; ?>
                </span>
            </div>
        </div>
    </div>
</body>
</html>