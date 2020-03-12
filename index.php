<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenTube</title>
</head>
<body>
    <h1>OpenTube</h1>
    <form action="download.php" method="post">
    YouTube hash: <input type="text" name="yt-hash"><br>
    <input type="submit">
    </form>
<?php
    if ($handle = opendir('videos')) {
        while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $ext = pathinfo($entry, PATHINFO_EXTENSION);
            echo "<h1>$entry</h1>";
            echo '<video width="320" height="240" controls>';
            echo "<source src=\"videos/$entry\" type=\"video/$ext\">";
            echo '
                Your browser does not support the video tag.
                </video>
            ';
        }
    }
    closedir($handle);
}
?>
</body>
</html>
