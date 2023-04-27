<?php

require_once 'tokens.php';

// TODO: add ratelimits here
//       this should be a healthcheck endpoint not a bruteforce endpoint

// prefer post for security
// fallback to get for easier debugging
$username = isset($_POST['username']) ? $_POST['username'] : $_GET['username'];
$token = isset($_POST['token']) ? $_POST['token'] : $_GET['token'];

if (is_valid_token($username, $token)) {
    echo "OK";
} else {
    echo "invalid token";
    http_response_code(404);
}
