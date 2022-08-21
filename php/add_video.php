<?php

require_once 'database.php';

/*
function add_video($video_path, $title) {
    if(!is_dir('php') || !is_dir('videos')) {
        echo 'php/ or videos/ not found are you in the right directory?';
        return false;
    }
    if(!file_exists($video_path)) {
        return false;
    }
    $hash = hash_file('sha256', $video_path);
    $db = new VideoDB;
    $stmt = $db->prepare('INSERT INTO Videos (Hash, Title) VALUES (:hash, :title)');
    $stmt->bindValue(':hash', $hash, SQLITE3_TEXT);
    $stmt->bindValue(':title', preg_replace('/^[a-zA-Z0-9\._-]+$/', '_', $title), SQLITE3_TEXT);
    $stmt->execute();
    mkdir('videos/users/test', 0777, true);
    rename($video_path, 'videos/users/test/' . $hash . '.' . pathinfo($video_path, PATHINFO_EXTENSION));
    return true;
}

add_video("videos/uploading/test.mp4", "test video");
*/

?>
