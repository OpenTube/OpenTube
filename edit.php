<!DOCTYPE html>
<html lang="en">
<head>
<?php require 'php/header.php'; ?>
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
            echo '<br>Error: "videos/downloaded" directory not found';
            return;
        }
        if (!is_file('videos/downloaded/' . $video)) {
            echo "<br>Error: file '$video' does not exist.<br>";
            return;
        }
        unlink('videos/downloaded/' . $video);
    }
    function delete_user_video($video, $user) {
        $video_dir = 'videos/users/' . $user . '/';
        if (!is_dir($video_dir)) {
            echo '<br>Error: "' . $video_dir . '" directory not found';
            return;
        }
        $video_path = $video_dir . $video;
        if (!is_file($video_path)) {
            echo '<br>Error: file "' . $video_path . '" does not exist.<br>';
            return;
        }
        if (!is_dir('videos/deleted')) {
            mkdir('videos/deleted', 0777, true);
        }
        $delete_dir = 'videos/deleted/' . $user . '/';
        if (!is_dir($delete_dir)) {
            mkdir($delete_dir, 0777, true);
        }
        $delete_path = $delete_dir . $video;
        if (is_file($delete_path)) {
            echo '<br>Error: file "' . $video . '" already in trash. Please empty your trash first.<br>';
            return;
        }

        if (!rename($video_path, $delete_path)) {
            echo "Error: deletion failed.<br>";
            echo "try running ./scripts/fix_permissions.sh and set the videos/ owner group to the web server group";
        } else {
            echo "moved video to trash (recoverable)<br>";
        }
    }
    if (isset($_GET['u'])) {
        if (isset($_GET['delete'])) {
            delete_user_video($_GET['delete'], $_GET['u']);
        }
    } else {
        if (isset($_GET['delete'])) {
            delete_video($_GET['delete']);
        } else if (isset($_GET['save'])) {
            save_video($_GET['save'], $user);
        }
    }
    echo '<br><a href="index.php">Okay</a><br>';
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
