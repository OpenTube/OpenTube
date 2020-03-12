<html>
<body>
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
</body>
</html>
