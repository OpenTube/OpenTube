<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenTube</title>
</head>
<h1>OpenTube</h1>
<?php
    if ($handle = opendir('videos')) {
        while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $ext = pathinfo($entry, PATHINFO_EXTENSION);
            echo '<video width="320" height="240" controls>';
            echo "<source src=\"videos/$entry\" type=\"video/$ext\">";
            echo '
                Your browser does not support the video tag.
                </video>
            ';
        }
    }
    closedir($handle);
}
?>
</body>
</html>
