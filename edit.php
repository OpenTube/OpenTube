<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/basic.css">
    <title>OpenTube</title>
</head>
<body>
    <div class="content">
    <h1><a href="index.php">OpenTube</a></h1>
<?php
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
    echo '<br><a href="index.php">Okay</a><br>';
    if (isset($_GET['delete'])) {
        delete_video($_GET['delete']);
    } else if (isset($_GET['save'])) {
        save_video($_GET['save']);
    }
?>
    </div> <!-- .content -->
    <div>
        <script src="js/main.js"></script>
        <footer>
            <a href="https://github.com/OpenTube/OpenTube">OpenTube</a> - todo add some cool lawyer sentence here
        </footer>
    </div>
</body>
</html>
