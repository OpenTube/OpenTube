<?php

require_once 'database.php';
require_once 'base.php';

class User {
    public $id;
    public $username;
    public $password;
    public $admin;

    function __construct($id, $username, $password, $admin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->admin = $admin;
    }

    function correct_password($pass) {
        return $this->password == $pass;
    }

    function is_admin() {
        return $this->admin;
    }

    function username() {
        return $this->username;
    }

    function id() {
        return $this->id;
    }
}

function get_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'invalid';
}

function create_user($username, $password) {
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO Accounts (Username, Password, Admin, RegisterDate, RegisterIP) VALUES (:username, :password, :admin, :register_date, :register_ip);');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':admin', 1);
    $stmt->bindValue(':register_date', get_date_str());
    $ip = get_ip();
    $stmt->bindValue(':register_ip', $ip);
    $res = $stmt->execute();
    if(!$res) {
        echo "user creation failed";
        die();
    }
    $user = get_user_by_name($username);
    echo "get user '$username' result: ";
    print_r($user);
    return $user;
}

function get_user_by_name($username) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM Accounts WHERE Username = :username');
    $stmt->bindValue(':username', $username);
    $res = $stmt->execute();
    if ($res->numColumns() == 0) {
        return null;
    }
    $row = $res->fetchArray(SQLITE3_ASSOC);
    $user = new User($row['ID'], $row['Username'], $row['Password'], $row['Admin']);
    return $user;
}

function login($username, $password) {
    $user = get_user_by_name($username);
    if (!$user) {
        echo "failed to login user does not exist";
        return null;
    }
    if (!$user->correct_password($password)) {
        echo "failed to login wrong password";
        return null;
    }
    echo "logged in!";
    return $user;
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
