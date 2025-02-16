<?php

function generate_random_string(int $length) : string {
    return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}