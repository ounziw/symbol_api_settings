<?php

function is_valid_address($address) {
    $validchars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return strlen($address) == strspn($address, $validchars);
}