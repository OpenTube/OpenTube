<?php

require_once 'tokens.php';

// TODO: add ratelimits here
//       this should be a healthcheck endpoint not a bruteforce endpoint

$username = $_POST['username'];
$token = $_POST['token'];

if (is_valid_token($usernam, $token)) {
    echo "OK";
} else {
    echo "invalid token";
    http_response_code(404);
}
