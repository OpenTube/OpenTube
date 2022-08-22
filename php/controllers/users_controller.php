<?php

require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../base.php';
require_once __DIR__ . '/../models/user.php';

function get_ip() {
    return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'invalid';
}

function create_user($username, $password) {
    if(strlen($username) > MAX_USERNAME_LEN) {
        echo "Error: username too long (max: " . MAX_USERNAME_LEN . ").";
        echo '<a href="register.php">back</a>';
        die();
    }
    if(strlen($password) > MAX_PASSWORD_LEN) {
        echo "Error: password too long (max: " . MAX_PASSWORD_LEN . ").";
        echo '<a href="register.php">back</a>';
        die();
    }
    if(strlen($password) < MIN_PASSWORD_LEN) {
        echo "Error: password too short (min: " . MIN_PASSWORD_LEN . ").";
        echo '<a href="register.php">back</a>';
        die();
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo "Error: username can only include numbers, letters and underscores.";
        echo '<a href="register.php">back</a>';
        die();
    }
    $user = get_user_by_name($username);
    if ($user) {
        echo "Error: username already in use.";
        echo '<a href="register.php">back</a>';
        die();
    }
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO Users (Username, Password, Admin, RegisterDate, RegisterIP) VALUES (:username, :password, :admin, :register_date, :register_ip);');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':password', $password);
    $stmt->bindValue(':admin', 0);
    $stmt->bindValue(':register_date', get_date_str());
    $ip = get_ip();
    $stmt->bindValue(':register_ip', $ip);
    $res = $stmt->execute();
    if(!$res) {
        echo "user creation failed";
        die();
    }
    $user = get_user_by_name($username);
    if ($user) {
        $videos_path = 'videos';
        if(!is_dir($videos_path)) {
            $videos_path = '../videos';
        }
        if(!is_dir($videos_path)) {
            echo "Error: " . __DIR__ . "/videos folder not found";
            die();
        }
        $user_dir = $videos_path . '/users/' . $user->username();
        if(!mkdir($user_dir, 0777, true)) {
            echo "Error: failed to create user directory!";
        }
    } else {
        echo "Error: user not found";
    }
    return $user;
}

function get_user_by_query($result) {
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
    $user = new User($id, $row['Username'], $row['Password'], $row['Admin']);
    return $user;
}

function get_user_by_name($username) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM Users WHERE Username = :username');
    $stmt->bindValue(':username', $username);
    return get_user_by_query($stmt->execute());
}

function get_user_by_id($id) {
    $db = get_db();
    $stmt = $db->prepare('SELECT * FROM Users WHERE ID = :id');
    $stmt->bindValue(':id', $id);
    return get_user_by_query($stmt->execute());
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
    $stmt = $db->prepare('SELECT Admin FROM Users WHERE Username = :username');
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
