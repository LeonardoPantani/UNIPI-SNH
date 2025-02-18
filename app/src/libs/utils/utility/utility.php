<?php

use Random\RandomException;

function generate_random_string(int $length) : string {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

function generate_random_integer(int $min, int $max) : ?int {
    try {
        $ret = random_int($min, $max);
    } catch (RandomException) {
        return null;
    }
    return $ret;
}