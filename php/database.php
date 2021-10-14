<?php

class VideoDB extends SQLite3 {
    function __construct() {
        $video_db_schema =<<<EOF
        CREATE TABLE IF NOT EXISTS Videos(
            ID              INTEGER     PRIMARY KEY     AUTOINCREMENT,
            Hash            TEXT        NOT NULL,
            Title           TEXT        NOT NULL,
            Views           INTEGER     DEFAULT 0
        );
        EOF;
        $this->open('db/videos.db');
        $this->query($video_db_schema);
    }
}

?>
