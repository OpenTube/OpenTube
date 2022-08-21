<?php
session_start();

require_once 'base.php';
require_once 'controllers/users_controller.php';

function render_form() {
?>
<?php
require_once 'session.php';
require 'navbar.php';
?>

<h1>register</h1>
<form action="register.php" method="post">
    <label for="username">username:</label>
    <input type="text" name="username" id="username">
    <label for="password">password:</label>
    <input type="password" name="password" id="password">
    <label for="token">alpha token (ask admin):</label>
    <input type="token" name="token" id="token">
    <button type="submit">register</button>
</form>

Want an admin account? Run this in your terminal:

<code style="white-space: pre;">
sqlite3 db/opentube.db
INSERT INTO Users (Username, Password, Admin, RegisterDate, RegisterIP) VALUES ("admin", "admin", 1, "<?php echo get_date_str(); ?>", "<?php echo get_ip(); ?>");
</code>

<?php
}

if (!empty($_POST['username']) and !empty($_POST['password']))
{
    $username = isset($_POST['username'])? $_POST['username'] : '';
    $password = isset($_POST['password'])? $_POST['password'] : '';
    $token = isset($_POST['token'])? $_POST['token'] : '';
    if ($token != ALPHA_TOKEN) {
        echo 'invalid alpha token <a href="register.php">OKAY</a>';
        die();
    }
    $user = create_user($username, $password);
    echo '<br>';

    if(!$user) {
        echo 'register failed <a href="register.php">OKAY</a>';
        $_SESSION['user'] = null;
        die();
    }

    $_SESSION['user'] = $user;
    echo 'registered and logged in <a href="../index.php">OKAY</a>';
} else {
    render_form();
}
?>
