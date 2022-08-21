<?php

require_once 'base.php';
require_once 'database.php';
require_once 'tokens.php';
require_once 'controllers/users_controller.php';
require_once 'controllers/videos_controller.php';


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
    if (!preg_match('/^[a-zA-Z0-9_-]+\.[a-zA-Z0-9_-]+$/', $filepath)) {
        echo "Error: illegal filename\n";
        die();
    }
    $filepath = __DIR__ . '/../videos/users/' . $user->username() . "/$filepath";
    if(!file_exists($filepath)) {
        echo "Error: file not found on server '$filepath' did your upload fail?\n";
        die();
    }
    $ext = pathinfo($filepath, PATHINFO_EXTENSION);
    if($ext != "mp4" && $ext != "webm") {
        echo "Error: invalid file extension '$ext'\n";
        die();
    }

    $hash = hash_file('sha256', $filepath);
    $video = get_video_by_hash($hash);
    if($video) {
        echo "Error: this exact video was already uploaded!\n";
        die();
    }
    $fileinfo = new SplFileInfo($filepath);
    $filename = $fileinfo->getFilename();

    $db = get_db();
    $stmt = $db->prepare('INSERT INTO Videos (UUID, Hash, UploadFilename, Title, Description, Source, UserID, UploadDate, UploaderIP) VALUES (:uuid, :hash, :filename, :title, :description, :source, :user_id, :date, :ip)');
    $slug_title = preg_replace('/[^a-zA-Z0-9\._-]/u', '_', $title);
    $stmt->bindValue(':uuid', guidv4(), SQLITE3_TEXT);
    $stmt->bindValue(':hash', $hash, SQLITE3_TEXT);
    $stmt->bindValue(':filename', $filename, SQLITE3_TEXT);
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
        echo "invalid token\n";
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
        $filepath = "TODO_FILE_PATH";
        add_video(session_user(), $filepath, $title, $description, $source);
    } else {
        if(empty($_POST['username']))
        {
            echo "missing field username\n";
            die();
        }
        if(empty($_POST['token']))
        {
            echo "missing field token\n";
            die();
        }
        if(empty($_POST['filepath']))
        {
            echo "missing field filepath\n";
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
<?php require 'navbar.php'; ?>

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
