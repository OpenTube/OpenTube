<h1><a href="<?php echo WEB_ROOT; ?>index.php">OpenTube</a></h1>

<?php
    $user = session_user();
    if ($user) {
        echo '<a href="' . WEB_ROOT. 'php/profile.php"> logged in as [' . $user->username() . ']</a>';
    } else {
        echo '<a href="' . WEB_ROOT. 'php/login.php">login</a>';
    }
?>
