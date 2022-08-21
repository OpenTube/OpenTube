<?php

require_once __DIR__ . '/../controllers/users_controller.php';
require_once __DIR__ . '/../database.php';

class Video {
    private $id;
    private $uuid;
    private $hash;
    private $title;
    private $description;
    private $views;
    private $user_id;
    private $user;

    function __construct($id, $uuid, $hash, $title, $description, $views, $user_id)
    {
        $this->id = $id;
        $this->uuid = $uuid;
        $this->hash = $hash;
        $this->title = $title;
        $this->description = $description;
        $this->views = $views;
        $this->user_id = $user_id;
        $this->user = null;
    }

    // lazy load user object
    function user() {
        if($this->user) {
            return $this->user;
        }
        $this->user = get_user_by_id($this->user_id);
    }

    function presist_view($user, $ip) {
        $user_id = $user ? $user->id() : -1;
        $db = get_db();
        $stmt = $db->prepare('INSERT INTO Views (VideoID, UserID, Date, ViewerIP) VALUES (:video_id, :user_id, :date, :ip);');
        $stmt->bindValue(':video_id', $this->id());
        $stmt->bindValue(':user_id', $user_id);
        $stmt->bindValue(':date', get_date_str());
        $stmt->bindValue(':ip', $ip);
        $stmt->execute();
        $this->views++;
    }

    function add_view($user, $ip) {
        $db = get_db();
        $stmt = $db->prepare('SELECT * FROM Views WHERE VideoID = :video_id AND ViewerIP = :viewer_ip ORDER BY Date DESC');
        $stmt->bindValue(':video_id', $this->id());
        $stmt->bindValue(':viewer_ip', $ip);
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        if (!$row) {
            $this->presist_view($user, $ip);
            return;
        }
        if (!$row['ID']) {
            $this->presist_view($user, $ip);
            return;
        }
        $now = strtotime(get_date_str());
        $last_view = strtotime($row['Date']);
        $diff = $now - $last_view;
        // only count views if at least 5min passed since last
        if($diff > 60 * 5) {
            echo "$diff";
            $this->presist_view($user, $ip);
        }
    }

    function views() {
        // TODO: use views cache in videos table
        // return $this->views;
        $db = get_db();
        $stmt = $db->prepare('SELECT COUNT(*) AS Sum FROM Views WHERE VideoID = :video_id');
        $stmt->bindValue(':video_id', $this->id());
        $res = $stmt->execute();
        $row = $res->fetchArray(SQLITE3_ASSOC);
        if (!$row) {
            return 0;
        }
        return $row['Sum'];
    }

    function uuid() {
        return $this->uuid;
    }

    function id() {
        return $this->id;
    }

    function save() {
        $db = get_db();
        $stmt = $db->prepare('UPDATE Videos SET Hash = :hash, Title = :title, Description = :description, Views = :views');
        $stmt->bindValue(':hash', $this->hash);
        $stmt->bindValue(':title', $this->title);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':views', $this->views);
        $stmt->execute();
    }
}

?>
