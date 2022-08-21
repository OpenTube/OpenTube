<?php
session_start();
?>
<h1><a href="index.php">OpenTube</a></h1>

<?php
    if ($_SESSION['user']) {
        echo '<a href="php/logout.php"> [' . $_SESSION['user']->username() . '] logout</a>';
    } else {
        echo '<a href="php/login.php">login</a>';
    }
?>
