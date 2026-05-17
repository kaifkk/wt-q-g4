<?php include 'app/config/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="app/public/assets/css/style.css?v=<?$version?>">
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

    </div>
</body>
</html>