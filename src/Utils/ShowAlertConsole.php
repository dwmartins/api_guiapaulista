<?php

function showSuccessLog($message, $time = false) {
    if($time) {
        echo "\n\033[42m[" . date('H:i:s') . "] $message\033[0m" . PHP_EOL . PHP_EOL;
        return;
    }

    echo "\n\033[42m$message\033[0m" . PHP_EOL . PHP_EOL;
}

function showAlertLog($message, $time = false) {
    if($time) {
        echo "\n\033[41m[" . date('H:i:s') . "] $message\033[0m" . PHP_EOL . PHP_EOL;
        return;
    }

    echo "\n\033[41m$message \033[0m" . PHP_EOL . PHP_EOL;
}

function showLog($message, $time = false) {
    if($time) {
        echo "[" . date('H:i:s') . "] $message". PHP_EOL;
        return;
    }

    echo "$message". PHP_EOL;
}