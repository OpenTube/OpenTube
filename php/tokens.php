<?php

require_once 'base.php';
require_once 'database.php';
require_once 'controllers/users_controller.php';
require_once 'session.php';

function is_valid_token($username, $token) {
    $db = get_db();
    $stmt = $db->prepare('SELECT ExpireDate FROM Tokens WHERE Username = :username AND UUID = :token');
    $stmt->bindValue(':username', $username);
    $stmt->bindValue(':token', $token);
    $res = $stmt->execute();
    if (!$res->numColumns()) {
        return false;
    }
    $row = $res->fetchArray(SQLITE3_ASSOC);
    if(!$row) {
        return false;
    }
    if(is_expired($row["ExpireDate"])) {
        return false;
    }
    $user = get_user_by_name($username);
    if (!$user) {
        return false;
    }
    return $user;
}

function generate_token($user, $name, $days_valid) {
    $db = get_db();
    $stmt = $db->prepare('INSERT INTO Tokens (UUID, Title, Username, UserID, ExpireDate, IssueDate, IssuerIp) VALUES (:uuid, :title, :username, :user_id, :expire_date, :issue_date, :ip);');
    $token = guidv4();
    $expire_date = get_date_obj();
    $expire_date->add(new DateInterval('P' . $days_valid . 'D')); // Peroid (days) Days
    $stmt->bindValue(':uuid', $token);
    $stmt->bindValue(':title', $name);
    $stmt->bindValue(':username', $user->username());
    $stmt->bindValue(':user_id', $user->id());
    $stmt->bindValue(':expire_date', get_date_str($expire_date));
    $stmt->bindValue(':issue_date', get_date_str());
    $stmt->bindValue(':ip', get_ip());

    $stmt->execute();
    return $token;
}

if(($_SERVER['REQUEST_METHOD'] === 'POST') && str_ends_with($_SERVER['SCRIPT_FILENAME'], '/php/tokens.php')) {
    if (empty($_POST['name'])) {
        echo "Error: missing field name<br>";
        echo '<a href="../index.php">back</a>\n';
        die();
    }
    if(!session_user()) {
        echo "Error you are not logged in<br>";
        echo '<a href="../index.php">back</a>';
        die();
    }
    $user = session_user();
    $token = generate_token($user, $_POST['name'], 90);
    echo "generated token: $token<br>";
    echo '<a href="../index.php">okay</a>';
}
?>
