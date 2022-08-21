<?php
session_start();

require_once 'controllers/users_controller.php';

function render_form() {
?>
<?php
require_once 'session.php';
require 'navbar.php';
?>

<h1>login</h1>
<form action="login.php" method="post">
    <label for="username">username:</label>
    <input type="text" name="username" id="username">
    <label for="password">password:</label>
    <input type="password" name="password" id="password">
    <button type="submit">login</button>
</form>

No account? Register <a href="register.php">here</a>.

<?php
}

if (!empty($_POST['username']) and !empty($_POST['password']))
{
    $username = isset($_POST['username'])? $_POST['username'] : '';
    $password = isset($_POST['password'])? $_POST['password'] : '';
    $user = login($username, $password);
    echo '<br>';

    if(!$user) {
        echo 'login failed <a href="login.php">OKAY</a>';
        $_SESSION['user'] = null;
        die();
    }

    $_SESSION['user'] = $user;
    echo 'logged in <a href="../index.php">OKAY</a>';
} else {
    render_form();
}
?>
