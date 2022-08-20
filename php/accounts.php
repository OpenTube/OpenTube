<?php

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
    $ret = $handle->exec($accounts_table);
    if($ret != 1) {
        echo "failed to create table accounts.";
        die();
    }
}

function connect_db() {
    check_sqlite_enabled();
    $handle = new SQLite3("db/accounts.db");
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

function create_user($username, $password) {
    $db = get_db();
    date_default_timezone_set("Europe/Berlin");
    $stmt = $db->prepare('INSERT INTO Accounts (Username, Password, Admin, RegisterDate, RegisterIP) VALUES (:username, :password, :admin, :register_date, :register_ip);');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':admin', 1);
    $stmt->bindValue(':register_date', date('d/m/Y H:i'));
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'invalid';
    $stmt->bindValue(':register_ip', $ip);
    $res = $stmt->execute();
}

function is_admin($username) {
    $db = get_db();
    $stmt = $db->prepare('SELECT Admin FROM Accounts WHERE Username = :username');
    $stmt->bindValue(':username', $username);
    $res = $stmt->execute();
    if ($res->numColumns()) {
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
            return $row["Admin"] == 1;
        }
    }
    return false;
}

?>
