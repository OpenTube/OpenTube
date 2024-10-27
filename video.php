<!DOCTYPE html>
<html lang="en">
<head>
<?php require 'php/header.php'; ?>
</head>
<body>
    <div class="content">
    <h1><a href="index.php">OpenTube</a></h1>
<?php
    require_once 'php/controllers/users_controller.php';
    require_once 'php/controllers/videos_controller.php';
    require_once 'php/session.php';

    function html_video_viewer($video_path) {
        $ext = pathinfo($video_path, PATHINFO_EXTENSION);
        $name = pathinfo($video_path, PATHINFO_FILENAME);
        echo '<video width="1280" height="720" controls class="video-main">';
        echo "<source src=\"$video_path\" type=\"video/$ext\">";
        echo '
        Your browser does not support the video tag.
        </video>
        ';
        echo "<h1>$name</h1>";
    }
    function play_video($title) {
        define('R_TITLE', '/^[a-zA-Z0-9\._-]+$/');
        if(!preg_match(R_TITLE, $title)) {
            echo "<br>ERROR INVALID VIDEO TITLE '$title'<br>";
            echo '<br><a href="index.php">Okay</a><br>';
            http_response_code(404);
            die();
        }
        $category = 'saved';
        if(isset($_GET['c'])) {
            if($_GET['c'] == 'saved' || $_GET['c'] == 'downloaded' || $_GET['c'] == 'unlisted') {
                $category = $_GET['c'];
            }
        }
        $video = null;
        if(isset($_GET['u'])) {
            $username = $_GET['u'];
            $category = 'users/' . $username;
            $user = get_user_by_name($username);
            if($user) {
                $video = get_video_by_user_and_filename($user, $title);
                if($video) {
                    $video->add_view(session_user(), get_ip());
                    $video->save();
                }
            }
        }
        if(str_contains($category, '.')) {
            echo "<br>CATEGORY CAN NOT CONTAIN DOTS<br>";
            echo '<br><a href="index.php">Okay</a><br>';
            http_response_code(400);
            die();
        }
        $path = "videos/$category/$title";
        if(!is_file($path)) {
            echo "<br>ERROR VIDEO NOT FOUND<br>";
            echo '<br><a href="index.php">Okay</a><br>';
            http_response_code(404);
            die();
        }
        html_video_viewer($path);
        if($video) {
            echo "views: " . $video->views();
        }
    }
    if(isset($_GET["t"])) {
        play_video($_GET["t"]);
    } else {
        echo "<br>ERROR<br>";
        echo '<br><a href="index.php">Okay</a><br>';
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
