<?php 
include 'app/config/config.php';
require_once 'app/models/contentModels.php';
session_start();
$contents = getHighlightedContents();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="app/public/assets/css/style.css?v=<?=$version?>">
</head>
<body>
    <div>
        <div class="navbar">
            <div class="mediaFTPnavbar">
                <img src="app/public/assets/icons/media.png" alt="mediaLogo">
                <h2>MediaFTP</h2>
            </div>
            <div>
                <input type="button" name="homeBtn" value="Home" />
                <input type="button" name="browseBtn" value="Browse" />
                <?php if(isset($_SESSION['user_id'])) { ?>
                        <input type="button" name="profileBtn" value="Profile" />
                <?php } ?>
                
            </div>
        </div>

        <div class="heroSectionCard">
            <h2>Free Media Downloads</h2>
            <p>Movies, Software, TV Series, Games - no account needed</p>
            <div>
                <input type="text" name="searchBox" placeholder="Search titles, descriptions.." class="searchBox" />
                <input type="button" name="searchBtn" value="Search" class="searchBtn" />
            </div>
        </div>

        <p>Most downloaded this week</p>

       

        <div class="heroSectionContent">
            <?php while($content = mysqli_fetch_assoc($contents)) { ?>
                <div class="contentCard">
                    <h3><?php echo htmlspecialchars($content['title']); ?></h3>
                    <p><?php echo htmlspecialchars($content['category_name']); ?></p>
                    <p><?php echo htmlspecialchars($content['description']); ?></p>
                    <div>
                        <span class="categoryTag">Category</span>
                        <input type="button" name="downloadBtn" value="Download" />
                    </div>
                </div>
            <?php } ?>
        </div>
        
    </div>
</body>
</html>