<?php

require_once 'config.php';

/*
    Date format used by OpenTube

    2022-08-21 16:59:58
*/

function get_date_str($date_obj = null) {
    date_default_timezone_set('Europe/Berlin');
    if($date_obj) {
        return $date_obj->format('Y-m-d H:i:s');
    }
    return date('Y-m-d H:i:s');
}

function get_date_obj($time_str = null) {
    if (!$time_str) {
        $time_str = get_date_str();
    }
    $date = new DateTime($time_str);
    $timezone = new DateTimeZone('Europe/Berlin');
    $date->setTimezone($timezone);
    return $date;
}

function is_expired($date_str) {
    $now = get_date_obj();
    $expire = get_date_obj($date_str);
    return $now > $expire;
}

function guidv4() {
    $data = random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

?>
