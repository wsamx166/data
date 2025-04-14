<?php

function isThrottled($user_id, $seconds = 5) {
    $file = __DIR__ . "/../throttle/$user_id.txt";
    if (!file_exists($file)) return false;

    $last = file_get_contents($file);
    if (time() - intval($last) < $seconds) {
        return true;
    }
    return false;
}

function updateThrottle($user_id) {
    $file = __DIR__ . "/../throttle/$user_id.txt";
    file_put_contents($file, time());
}
