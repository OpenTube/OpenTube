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
    function html_video_viewer($video, $saved) {
        $ext = pathinfo($video, PATHINFO_EXTENSION);
        echo "<h1>$video</h1>";
        echo '<video width="320" height="240" controls>';
        echo "<source src=\"" . (($saved) ? "saved_" : "") . "videos/$video\" type=\"video/$ext\">";
        echo '
            Your browser does not support the video tag.
            </video>
        ';
    }
    function html_video_buttons($video) {
        echo "<br><a href='/index.php/?delete=$video'>DELETE</a><br>";
        echo "<br><a href='/index.php/?save=$video'>SAVE</a><br>";
    }
    function save_video($video) {
        if (!is_dir('saved_videos')) {
            mkdir('saved_videos', 0777, true);
        }
        $full_path = 'videos/' . $video;
        if (!is_file($full_path)) {
            echo "<br>Error: file '$full_path' does not exist.<br>";
            return;
        }
        rename($full_path, 'saved_videos/' . $video);
    }
    function delete_video($video) {
        if (!is_dir('videos')) {
            echo "<br>Error: videos directory not found";
            return;
        }
        if (!is_file('videos/' . $video)) {
            echo "<br>Error: file '$video' does not exist.<br>";
            return;
        }
        unlink('videos/' . $video);
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
    if (isset($_GET['delete'])) {
        delete_video($_GET['delete']);
    } else if (isset($_GET['save'])) {
        save_video($_GET['save']);
    }
?>
</body>
</html>
