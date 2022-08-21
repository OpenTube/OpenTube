<?php

if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle, $case = true)
    {
        $expectedPosition = strlen($haystack) - strlen($needle);
        if ($case) {
            return strrpos($haystack, $needle, 0) === $expectedPosition;
        }
        return strripos($haystack, $needle, 0) === $expectedPosition;
    }
}
