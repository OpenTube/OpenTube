<?php
session_start();

function session_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function session_is_admin() {
    $user = session_user();
    if (!$user) {
        return false;
    }
    return $user->is_admin();
}

?>
