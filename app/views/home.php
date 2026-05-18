<?php
session_start();
require_once '../models/contentModel.php';
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediaFTP - Home</title>
    <link rel="stylesheet" href="../../public/assets/css/style.css"> 
    <style>
        .home-container { padding: 30px; color: #fff; }
        .content-card { background: rgba(255,255,255,0.1); padding: 15px; margin: 10px 0; border-radius: 8px; }
        .request-box { background: rgba(0,0,0,0.5); padding: 20px; border-radius: 8px; margin-top: 30px; width: 400px; }
        .err-msg { color: red; font-size: 12px; }
        .search-inputs { padding: 10px; border-radius: 4px; border: none; margin-right: 10px; }
    </style>
</head>
<body style="background: #2c3e50;">
    <div>
        <div class="navbar">
            <div class="mediaFTPnavbar">
                <img src="../../public/assets/icons/media.png" alt="mediaLogo">
                <h2>MediaFTP</h2>
            </div>
            <div class="navbarLoginRegisterButtonAuthenticationForm">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" style="color:white; text-decoration:none;">Dashboard</a>
                <?php else: ?>
                    <input type="button" value="Login" onclick="window.location.href='login.php'">
                    <input type="button" value="Register" onclick="window.location.href='register.php'">
                <?php endif; ?>
            </div>
        </div>

        <div class="home-container">
            <h2>Public Media Archive</h2>
            
            <input type="text" id="searchBar" class="search-inputs" placeholder="Search title or description..." onkeyup="performSearch()" style="width:300px;">
            <select id="categoryFilter" class="search-inputs" onchange="performSearch()">
                <option value="">All Categories</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <br><br>

            <div id="contentGrid">
                </div>

            <div class="request-box">
                <form id="requestForm" onsubmit="event.preventDefault(); submitRequest();">
                    <h3>Request Missing Content</h3>
                    <p id="sys-msg"></p>
                    
                    <p>Content Title</p>
                    <input type="text" id="req_title" class="search-inputs" style="width:90%;">
                    <span class="err-msg" id="err-title"></span>

                    <p>Category</p>
                    <select id="req_category" class="search-inputs" style="width:95%;">
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']) ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                        <option value="Other">Other</option>
                    </select>
                    <span class="err-msg" id="err-category"></span>

                    <p>Message</p>
                    <textarea id="req_message" rows="3" class="search-inputs" style="width:90%;"></textarea>
                    
                    <br><br>
                    <input type="submit" value="Submit Request" style="padding:10px; background:#3498db; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                </form>
            </div>
        </div>
    </div>

    <script src="../../public/assets/js/member.js"></script>
    <script> window.onload = performSearch; </script>
</body>
</html>