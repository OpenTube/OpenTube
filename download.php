<?php
    function disable_ob() {
        // Turn off output buffering
        ini_set('output_buffering', 'off');
        // Turn off PHP output compression
        ini_set('zlib.output_compression', false);
        // Implicitly flush the buffer(s)
        ini_set('implicit_flush', true);
        ob_implicit_flush(true);
        // Clear, and turn off output buffering
        while (ob_get_level() > 0) {
            // Get the curent level
            $level = ob_get_level();
            // End the buffering
            ob_end_clean();
            // If the current level has not changed, abort
            if (ob_get_level() == $level) break;
        }
        // Disable apache output buffering/compression
        if (function_exists('apache_setenv')) {
            apache_setenv('no-gzip', '1');
            apache_setenv('dont-vary', '1');
        }
    }
    function verbose_exec($cmd) {
        disable_ob();
        $descriptorspec = array(
        0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
        2 => array("pipe", "w")    // stderr is a pipe that the child will write to
        );
        flush();
        $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
        echo "<pre>";
        if (is_resource($process)) {
            while ($s = fgets($pipes[1])) {
                print $s;
                flush();
            }
        }
        echo "</pre>";
    }
    function download_video($hash) {
        define('R_YT_HASH', '/^[a-zA-Z0-9_-]+$/');
        if(!preg_match(R_YT_HASH, $hash)) {
            echo "<br>ERROR INVALID YOUTUBE HASH<br>";
            return;
        }
        echo "<br>downloading '$hash' ...<br>";
        verbose_exec("mkdir -p videos && cd videos && /usr/local/bin/youtube-dl -f mp4 https://www.youtube.com/watch?v=$hash");
        echo "<br>finished '$hash' ...<br>";
        echo '<br><a href="index.php">Okay</a><br>';
    }
    if (isset($_POST["yt-hash"])) {
        download_video($_POST["yt-hash"]);
    }
?>