<?php

if (!function_exists('rupiah')) {
    function rupiah($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}