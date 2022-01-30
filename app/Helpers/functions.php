<?php

if (!function_exists('generateRandomCode')) {

    function generateRandomCode($type, $lenght): string
    {

        if ($type === 1) {
            $input = '1234567890';
        }
        if ($type === 2) {
            $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }
        if ($type === 3) {
            $input = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@&?^%';
        }
        if ($type === 4) {
            $input = '0123456789abcdefghijklmnopqrstuvwxyz';
        }

        return substr(str_shuffle($input), 0, $lenght);
    }
}
