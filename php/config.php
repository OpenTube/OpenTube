<?php

// TODO: use dotfiles

if (file_exists('custom/config.php')) {
    require 'custom/config.php';
} else {
    require 'config_default.php';
}

?>
