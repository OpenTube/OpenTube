<?php

require_once 'session.php';
require_once 'base.php';
require_once 'database.php';
require_once 'tokens.php';
require_once 'accounts.php';


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

function add_video($user, $filepath, $title, $description, $source) {
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO Videos (UUID, Hash, Title, Description, Source, UserID, UploadDate, UploaderIP) VALUES (:uuid, :hash, :title, :description, :source, :user_id, :date, :ip)');
    $slug_title = preg_replace('/[^a-zA-Z0-9\._-]/u', '_', $title);
    $stmt->bindValue(':uuid', guidv4(), SQLITE3_TEXT);
    $stmt->bindValue(':hash', hash_file('sha256', $filepath), SQLITE3_TEXT);
    $stmt->bindValue(':title', $slug_title, SQLITE3_TEXT);
    $stmt->bindValue(':description', $description, SQLITE3_TEXT);
    $stmt->bindValue(':source', $source, SQLITE3_TEXT);
    $stmt->bindValue(':user_id', $user->id());
    $stmt->bindValue(':date', get_date_str());
    $stmt->bindValue(':ip', get_ip());

    $stmt->execute();
}

function add_video_token($username, $token, $filepath, $title, $description, $source) {
    $user = is_valid_token($username, $token);
    if(!$user) {
        echo "invalid token";
        die();
    }
    add_video($user, $filepath, $title, $description, $source);
}

if (!empty($_POST['title']) and !empty($_POST['description']))
{
    $title = $_POST['title'];
    $description = $_POST['description'];
    $source = isset($_POST['source']) ? $_POST['source'] : '';
    if(session_user()) {
        echo "session user found";
        $filepath = "TODO_FILE_PATH";
        add_video(session_user(), $filepath, $title, $description, $source);
    } else {
        if(empty($_POST['username']))
        {
            echo "missing field username";
            die();
        }
        if(empty($_POST['token']))
        {
            echo "missing field token";
            die();
        }
        if(empty($_POST['filepath']))
        {
            echo "missing field filepath";
            die();
        }
        $username = $_POST['username'];
        $token = $_POST['token'];
        $filepath = $_POST['filepath'];
        add_video_token($username, $token, $filepath, $title, $description, $source);
    }
}
else
{
?>

<h1>Upload video</h1>
<form action="add_video.php" method="post">
    <label for="title">title:</label>
    <input type="text" name="title" id="title">
    <label for="description">description:</label>
    <input type="description" name="description" id="description">
    <label for="filepath">filepath (TODO: make this a hidden field or guess it based on file upload name):</label>
    <input type="filepath" name="filepath" id="filepath">
    <button type="submit">register</button>
</form>

<code style="white-space: pre;">
curl 'http://localhost:8080/php/add_video.php' \
    -X POST \
    -H 'Content-Type: application/x-www-form-urlencoded' \
    --data-raw 'title=mytitle&description=mydesc&filepath=filepath_on_srv&token=mytoken&username=myusername'
</code>

<?php
}
?>
