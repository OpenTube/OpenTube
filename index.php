<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/basic.css">
    <title>OpenTube</title>
</head>
<body>
    <h1><a href="index.php">OpenTube</a></h1>
    <!--
    <form action="download.php" method="post">
    YouTube hash: <input type="text" name="yt-hash"><br>
    <input type="submit">
    </form>
    -->
<?php
    function html_video_viewer($video, $saved) {
        $ext = pathinfo($video, PATHINFO_EXTENSION);
        $name = pathinfo($video, PATHINFO_FILENAME);
        echo "<h1>";
        if($saved) {
            echo "<a href=\"video.php?t=$video\">$name</a>";
        } else {
            echo "$name";
        }
        echo "</h1>";
        echo '<video width="320" height="240" controls>';
        echo "<source src=\"" . (($saved) ? "saved_" : "") . "videos/$video\" type=\"video/$ext\">";
        echo '
            Your browser does not support the video tag.
            </video>
        ';
    }
    function html_video_buttons($video) {
        echo "<br><a href=\"edit.php?delete=$video\">DELETE</a><br>";
        echo "<br><a href=\"edit.php?save=$video\">SAVE</a><br>";
    }
    function html_video($video, $saved) {
        html_video_viewer($video, $saved);
        if (!$saved) {
            html_video_buttons($video);
        }
    }
    function list_video_dir($dir, $saved) {
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    html_video($entry, $saved);
                }
            }
            closedir($handle);
        }
    }
    list_video_dir('videos', false);
    list_video_dir('saved_videos', true);
?>
</body>
</html>
