<?php

/*
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
*/

$_db_handle = null;

function check_sqlite_enabled() {
    if(!extension_loaded('SQLite3')) {
        $inipath = php_ini_loaded_file();
        if ($inipath) {
            echo 'Loaded php.ini: ' . $inipath . '<br>';
        } else {
            echo 'A php.ini file is not loaded' . '<br>';
        }
        echo "Could not load SQLite3 extension!" . '<br>';
        echo "Make sure php with sqlite3 support is installed and activated." . '<br>';
        echo "Open your php.ini file and uncomment the following lines:" . '<br>';
        echo '<code style="white-space: pre;">';
        echo "  extension=pdo_sqlite" . '<br>';
        echo "  extension=sqlite3" . '<br>';
        echo '</code>';
    }
}

function create_table($name, $handle, $schema) {
    $ret = $handle->exec($schema);
    if($ret != 1) {
        echo "failed to create table $name.";
        die();
    }
}

function create_tables($handle) {
    $accounts_table =<<<EOF
	CREATE TABLE IF NOT EXISTS Accounts
	(
	  ID            INTEGER   PRIMARY KEY    AUTOINCREMENT,
	  Username      TEXT,
	  Password      TEXT,
	  Admin         INTEGER,
	  RegisterDate  TEXT,
	  RegisterIP    TEXT
	);
	EOF;
    /*
        As of right now Titles have to be unique and are the identifier.
        In the future tho it is planned to support duplicated titles.
        And video renaming. So a hash of the video file is needed.

        Also at some point video edits might be a thing.
        Thus a UUID is needed. Video filenames could then be: UUID_Hash.mp4


        The "Source" column is a optional field containing the video source link.
        For example if it was downloaded from youtube this would be the youtube link.
    */
    $videos_table =<<<EOF
	CREATE TABLE IF NOT EXISTS Videos
	(
	  ID            INTEGER   PRIMARY KEY    AUTOINCREMENT,
	  UUID          TEXT,
	  Hash          TEXT,
	  Description   TEXT,
	  Title         TEXT,
	  Source        TEXT,
	  Views         INTEGER,
	  Deleted       INTEGER,
	  UserID        INTEGER,
	  UploadDate    TEXT,
	  UploaderIP    TEXT
	);
	EOF;
    create_table("accounts", $handle, $accounts_table);
    create_table("videos", $handle, $videos_table);
}

function connect_db() {
    check_sqlite_enabled();
    $handle = new SQLite3(__DIR__ . "/../db/opentube.db");
    if(!$handle) {
        echo $handle->lastErrorMsg();
        die();
    }
    return $handle;
}

function get_db() {
    GLOBAL $_db_handle;
    if ($_db_handle) {
        return $_db_handle;
    }
    $_db_handle = connect_db();
    create_tables($_db_handle);
    return $_db_handle;
}

?>
