    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/basic.css">
    <title>OpenTube</title>

<?php
    if (file_exists('css/custom.css')) {
        echo '<link rel="stylesheet" type="text/css" href="css/custom.css">';
    }
    if (file_exists('custom/header.php')) {
        require 'custom/header.php';
    }
?>
