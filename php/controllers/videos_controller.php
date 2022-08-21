<?php

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../models/video.php';

function get_video_by_query($result) {
    if ($result->numColumns() == 0) {
        return null;
    }
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if (!$row) {
        return null;
    }
    $id = $row['ID'];
    if (!$id) {
        return null;
    }
    $user = new Video($id, $row['UUID'], $row['Hash'], $row['Title'], $row['Description'], $row['Views'], $row['UserID']);
    return $user;
}

function get_video_by_hash($hash) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM Videos WHERE Hash = :hash');
    $stmt->bindValue(':hash', $hash);
    return get_video_by_query($stmt->execute());
}

function get_video_by_user_and_filename($user, $title) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM Videos WHERE UserID = :user_id AND UploadFilename = :title');
    $stmt->bindValue(':user_id', $user->id());
    $stmt->bindValue(':title', $title);
    return get_video_by_query($stmt->execute());
}
