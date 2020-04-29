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
    <!--
    <form action="download.php" method="post">
    YouTube hash: <input type="text" name="yt-hash"><br>
    <input type="submit">
    </form>
    -->
    <form action="index.php" method="get">
        sarch: <input type="text" name="s" value="<?php echo $_GET['s']; ?>"><br>
        <input type="submit">
    </form>
<?php
    function str_contains($haystack, $needle) {
        $needlePos = strpos(
            strtolower($haystack),
            strtolower($needle)
        );
        return ($needlePos === false ? false : ($needlePos+1));
    }
    function html_video_viewer($video, $saved) {
        $ext = pathinfo($video, PATHINFO_EXTENSION);
        $name = pathinfo($video, PATHINFO_FILENAME);
        echo "<h1>";
        if($saved) {
            echo "<a href=\"video.php?t=$video\">$name</a>";
        } else {
            echo "$name";
        }
        // keep extension since extension matters also for urls
        $thumbnail = "thumbnails/$name.$ext.png";
        echo "</h1>";
        echo "<video width=\"320\" height=\"240\" controls poster=\"$thumbnail\">";
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
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 0;
        $per_page = isset($_GET['pp']) ? (int)$_GET['pp'] : 5;
        $search = isset($_GET['s']) ? $_GET['s'] : false;
        $total_videos = 0;
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry == "." || $entry == "..")
                    continue;
                if ($search && !str_contains(pathinfo($entry, PATHINFO_FILENAME), $search))
                    continue;
                $total_videos++;
                if($total_videos > $page * $per_page && $total_videos <= $page * $per_page + $per_page)
                    html_video($entry, $saved);
            }
            closedir($handle);
        }
        // TODO: BIG REFACTOR PLEASE OR JUST USE A DATABASE FINALLY
        if ($total_videos == 0)
            return;
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
        }
        for($i = $start_page; $i <= $end_page; $i++) {
            if ($page === $i)
            echo "<a class=\"current-page\" href=\"index.php?p=$i&pp=$per_page&s=$search\">$i</a>";
            else
            echo "<a href=\"index.php?p=$i&pp=$per_page&s=$search\">$i</a>";
        }
        echo '</div>';
        echo "start: $start_page end: $end_page";
        echo "<span>[total: $total_videos]</span>";
    }
    list_video_dir('videos', false);
    list_video_dir('saved_videos', true);
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