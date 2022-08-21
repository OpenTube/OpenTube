<?php

require_once 'base.php';
require_once 'database.php';
require_once 'controllers/users_controller.php';
require_once 'session.php';

if(!session_user()) {
    header('Location: login.php');
}

$user = session_user();

?>
<?php require 'navbar.php'; ?>

<h1><?php echo $user->username(); ?></h1>

<h1>Generate Token</h1>
<form action="tokens.php" method="post">
    <label for="name">name:</label>
    <input type="text" name="name" id="name">
    <button type="submit">generate</button>
</form>

<a href="add_video.php">upload video</a><br>
<a href="logout.php">logout</a><br>
