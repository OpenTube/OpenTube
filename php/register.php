<?php
session_start();

require_once 'accounts.php';

function render_form() {
?>

<h1>register</h1>
<form action="register.php" method="post">
    <label for="username">username:</label>
    <input type="text" name="username" id="username">
    <label for="password">password:</label>
    <input type="password" name="password" id="password">
    <button type="submit">register</button>
</form>

Want a admin account? Run this in your terminal:

<code style="white-space: pre;">
sqlite3 db/opentube.db
INSERT INTO Accounts (Username, Password, Admin, RegisterDate, RegisterIP) VALUES ("admin", "admin", 1, "<?php date_default_timezone_set("Europe/Berlin");echo date('d/m/Y H:i'); ?>", "<?php echo isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'invalid'; ?>");
</code>

<?php
}

if (!empty($_POST['username']) and !empty($_POST['password']))
{
    $username = isset($_POST['username'])? $_POST['username'] : '';
    $password = isset($_POST['password'])? $_POST['password'] : '';
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
