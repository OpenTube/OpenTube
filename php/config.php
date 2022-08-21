<?php

// TODO: use dotfiles

if (file_exists(__DIR__ . '/../custom/config.php')) {
    require __DIR__ . '/../custom/config.php';
} else {
    require __DIR__ . '/../config_default.php';
}

?>
