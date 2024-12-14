<?php


if (!function_exists('calculatePercent')) {
    function calculatePercent($totalAmount, $percentage): float|int
    {
        return $totalAmount * ($percentage / 100);
    }
}

if (!function_exists('moneyFormat')) {
    function moneyFormat(float $amount): string
    {
        return number_format($amount, 2, '.', ' ');
    }
}
