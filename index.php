<!DOCTYPE html>
<html lang="en">
<head>
<?php require 'php/header.php'; ?>
<?php require 'php/controllers/users_controller.php'; ?>
<?php require 'php/session.php'; ?>
</head>
<body>
    <div class="content">
    <?php require 'php/navbar.php'; ?>
    <?php
    if (file_exists('custom/pre_index.php')) {
        require 'custom/pre_index.php';
    }
    ?>
    <!--
    <form action="download.php" method="post">
    YouTube hash: <input type="text" name="yt-hash"><br>
    <input type="submit">
    </form>
    -->
    <form action="index.php" method="get">
        sarch: <input type="text" name="s" value="<?php echo isset($_GET['s']) ? $_GET['s'] : ''; ?>">
        <input type="hidden" name="pp" value="<?php echo isset($_GET['pp']) ? (int)$_GET['pp'] : 5; ?>">
        <?php
            if (isset($_GET['u'])) {
                echo '<input type="hidden" name="u" value="' . $_GET['u'] . '">';
            }
        ?>
        <br>
        <input type="submit">
    </form>
<?php
    if (!function_exists('str_contains')) {
        function str_contains($haystack, $needle) {
            $needlePos = strpos(
                strtolower($haystack),
                strtolower($needle)
            );
            return ($needlePos === false ? false : ($needlePos+1));
        }
    }
    function html_video_viewer($video, $category, $user) {
        $ext = pathinfo($video, PATHINFO_EXTENSION);
        $name = pathinfo($video, PATHINFO_FILENAME);
        echo "<h3>";
        if ($category == 'users') {
            echo "<a href=\"video.php?t=$video&u=$user\">$name</a>";
        } else {
            echo "<a href=\"video.php?t=$video&c=$category\">$name</a>";
        }
        // keep extension since extension matters also for urls
        $thumbnail = "thumbnails/" . ($user ? "users/$user" : $category) . "/$name.$ext.png";
        echo "</h3>";
        echo "<video width=\"320\" height=\"240\" controls poster=\"$thumbnail\">";
        echo "<source src=\"videos/" . ($user ? "users/$user" : $category) . "/$video\" type=\"video/$ext\">";
        echo '
            Your browser does not support the video tag.
            </video>
        ';
    }
    function html_video_buttons($video, $user) {
        if ($user) {
            html_user_video_buttons($video, $user);
            return;
        }
        // downloads dir buttons:
        echo "<br><a href=\"edit.php?delete=$video\">DELETE</a><br>";
        echo "<br><a href=\"edit.php?save=$video\">SAVE</a><br>";
    }
    function html_user_video_buttons($video, $user) {
        echo "<br><a href=\"edit.php?u=$user&delete=$video\">DELETE</a><br>";
    }
    function html_video($video, $category, $user, $editable) {
        echo '<div class="video-container">';
        echo '  <div class="video-viewer">';
        html_video_viewer($video, $category, $user);
        echo '  </div>';
        if ($editable) {
            echo '  <div class="video-buttons">';
            html_video_buttons($video, $user);
            echo '  </div>';
        }
        echo '</div>';
    }
    function list_video_dir($category, $user, $editable) {
        $dir = 'videos/' . ($user ? "users/$user" : $category);
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
        $per_page = isset($_GET['pp']) ? (int)$_GET['pp'] : 5;
        $search = isset($_GET['s']) ? $_GET['s'] : false;
        $total_videos = 0;
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..") {
                    continue;
                }
                if (str_ends_with($entry, ".txt")) {
                    continue;
                }
                if ($search && !str_contains(pathinfo($entry, PATHINFO_FILENAME), $search)) {
                    continue;
                }
                $total_videos++;
                if($total_videos > $page * $per_page && $total_videos <= $page * $per_page + $per_page) {
                    html_video($entry, $category, $user, $editable);
                }
            }
            closedir($handle);
        }
        // TODO: BIG REFACTOR PLEASE OR JUST USE A DATABASE FINALLY
        if ($total_videos == 0) {
            return;
        }
        echo '<div class="pages">';
        $float_pages = $total_videos / $per_page;
        $int_pages = (int)$float_pages;
        if ($float_pages <= $int_pages) {
            $int_pages = $int_pages - 1;
        }
        $MAX_PAGES = 2; // in both directions
        $start_page = $page - $MAX_PAGES;
        $end_page = min($int_pages, $page + $MAX_PAGES);
        if ($start_page < 0) {
            $end_page -= $start_page;
            $start_page = 0;
            if ($end_page > $int_pages) {
                $end_page = $int_pages;
            }
        }
        $user_param = '';
        $search_param = '';
        if (isset($_GET['u'])) {
            $user_param = '&u=' . $_GET['u'];
        }
        if (isset($_GET['s'])) {
            $search_param = '&s=' . $_GET['s'];
        }
        for($i = $start_page; $i <= $end_page; $i++) {
            if ($page === $i) {
                echo "<a class=\"current-page\" href=\"index.php?p=$i&pp=$per_page$user_param$search_param\">$i</a>";
            } else {
                echo "<a href=\"index.php?p=$i&pp=$per_page$user_param$search_param\">$i</a>";
            }
        }
        echo '</div>';
        echo "<span>[videos: $total_videos pages: $int_pages]</span>";
    }
    function preview_users() {
        $users_path = 'videos/users';
        if (!is_dir($users_path)) {
            mkdir($users_path, 0777, true);
            return;
        }
        $user_dir = new DirectoryIterator($users_path);
        $num_users = 0;
        echo '<div class="users">';
        foreach ($user_dir as $fileinfo) {
            if (++$num_users > 15) {
                break;
            }
            if ($fileinfo->isDir() && !$fileinfo->isDot()) {
                $user = $fileinfo->getFilename();
                echo '  <a href="index.php?u=' . $user . '" class="user">' . $user . '</a>';
            }
        }
        echo '</div>';
    }
    if (isset($_GET['u'])) {
        $user = $_GET['u'];
        echo '<div class="user-banner">';
        echo '  <h2>' . $user . '</h2>';
        echo '</div>';
        list_video_dir('users', $user, session_is_admin());
    } else {
        preview_users();
        if(is_dir('videos/downloaded')) {
            list_video_dir('downloaded', null, true);
        }
        if(is_dir('videos/saved')) {
            list_video_dir('saved', null, false);
        }
    }
    if (file_exists('custom/post_index.php')) {
        require 'custom/post_index.php';
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
