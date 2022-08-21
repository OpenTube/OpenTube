<?php
session_start();

function session_user() {
    return $_SESSION['user'];
}

function session_is_admin() {
    $user = session_user();
    if (!$user) {
        return false;
    }
    return $user->is_admin();
}

?>
