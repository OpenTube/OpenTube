<!DOCTYPE html>
<html lang="en">
<head>
<?php require 'php/head.php'; ?>
</head>
<body>
    <div class="content">
    <h1><a href="index.php">OpenTube</a></h1>
<?php
    function save_video($video) {
        if (!is_dir('videos/saved')) {
            mkdir('videos/saved', 0777, true);
        }
        $full_path = 'videos/' . $video;
        if (!is_file($full_path)) {
            echo "<br>Error: file '$full_path' does not exist.<br>";
            return;
        }
        rename($full_path, 'videos/saved/' . $video);
    }
    function delete_video($video) {
        if (!is_dir('videos/downloaded')) {
            echo "<br>Error: videos/downloaded directory not found";
            return;
        }
        if (!is_file('videos/downloaded&' . $video)) {
            echo "<br>Error: file '$video' does not exist.<br>";
            return;
        }
        unlink('videos/downloaded/' . $video);
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
            <a href="https://github.com/OpenTube/OpenTube">OpenTube</a> - PUBLIC DOMAIN (Unlicense)
        </footer>
    </div>
</body>
</html>
