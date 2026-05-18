<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require __DIR__ . '/config/config.php'; 

require __DIR__ . '/models/contentModel.php'; 
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediaFTP - Home</title>
    <link rel="stylesheet" href="public/assets/css/style.css?v=<?= $version ?>">
</head>
<body>
    <div>
        <div class="navbar">
            <div class="mediaFTPnavbar">
                <img src="public/assets/icons/media.png" alt="mediaLogo">
                <h2>MediaFTP</h2>
            </div>
            <div class="navbarLoginRegisterButtonAuthenticationForm">
                <input type="button" name="homeBtn" value="Home" onclick="window.location.href='index.php'" />
                <?php if(isset($_SESSION['user_id'])): ?>
                    <input type="button" name="dashboardBtn" value="Dashboard" onclick="window.location.href='app/views/dashboard.php'" />
                    <input type="button" name="profileBtn" value="Profile" onclick="window.location.href='views/profile.php'" />
                    <input type="button" name="logoutBtn" value="Logout" onclick="window.location.href='controllers/logoutController.php'" /> 
                <?php endif; ?>
            </div>
        </div>

        <div class="heroSectionCard" style="text-align: center; padding: 50px 20px; height: auto;">
            <h2>Free Media Downloads</h2>
            <p>Movies, Software, TV Series, Games - no account needed</p>
            
            <div style="display: flex; justify-content: center; gap: 10px; margin-top: 20px;">
                <input type="text" id="searchBar" placeholder="Search titles, descriptions.." onkeyup="performSearch()" style="width: 300px; border-radius: 5px; background-color: #30302E; color: white; padding: 10px; border: 1px solid #41413E; outline: none;" />
                
                <select id="categoryFilter" onchange="performSearch()" style="border-radius: 5px; padding: 10px; background-color: #30302E; color: white; border: 1px solid #41413E; outline: none;">
                    <option value="">All Categories</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h2 id="contentSectionTitle" style="text-align: center; padding: 20px 0 0; margin: 0;">Most Downloaded</h2>

        <div id="contentGrid" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; padding: 20px 20px 30px;">
            <p>Loading contents...</p>
        </div>

        <div style="padding: 40px 20px; text-align: center;">
            <h2>Can't find what you're looking for?</h2>
            
            <div class="registrationFormCard" style="max-width: 500px; margin: 0 auto; text-align: left;">
                <h3 style="margin-top: 0; color: white;">Request Missing Content</h3>
                <p id="sys-msg"></p>
                <form id="requestForm" onsubmit="event.preventDefault(); submitRequest();">
                    
                    <div class="registrationFormInputs">
                        <p style="margin-bottom: 5px;">Content Title *</p>
                        <input type="text" id="req_title" style="background-color: #30302E; color: white; padding: 10px; border: none; border-radius: 4px; outline: none; width: 100%; box-sizing: border-box;" required>
                        <span id="err-title" style="color: #ff6b6b; font-size: 12px;"></span>
                    </div>

                    <div class="registrationFormInputs" style="margin-top: 15px;">
                        <p style="margin-bottom: 5px;">Category *</p>
                        <select id="req_category" style="background-color: #30302E; color: white; padding: 10px; border: none; border-radius: 4px; outline: none; width: 100%; box-sizing: border-box;" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                            <option value="Other">Other</option>
                        </select>
                        <span id="err-category" style="color: #ff6b6b; font-size: 12px;"></span>
                    </div>

                    <div class="registrationFormInputs" style="margin-top: 15px;">
                        <p style="margin-bottom: 5px;">Additional Message</p>
                        <textarea id="req_message" rows="3" style="background-color: #30302E; color: white; padding: 10px; border: none; border-radius: 4px; outline: none; width: 100%; box-sizing: border-box;"></textarea>
                    </div>

                    <div style="margin-top: 25px;">
                        <input type="button" value="Submit Request" onclick="submitRequest()" style="width: 100%;">
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="public/assets/js/member.js"></script>
    <script>
        window.onload = performSearch;
    </script>
</body>
</html>