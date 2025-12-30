<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format a number into K, M, B, T format for better readability
     * 
     * @param int|float $number The number to format
     * @return string The formatted number with suffix
     */
    public static function moneyfy($number): string
    {
        if ($number >= 1_000_000_000_000) {
            return round($number / 1_000_000_000_000, 2) . 'T';
        } elseif ($number >= 1_000_000_000) {
            return round($number / 1_000_000_000, 2) . 'B';
        } elseif ($number >= 1_000_000) {
            return round($number / 1_000_000, 2) . 'M';
        } elseif ($number >= 1_000) {
            return round($number / 1_000, 2) . 'K';
        } else {
            return (string)$number;
        }
    }

    /**
     * Format currency with proper formatting
     * 
     * @param int|float $amount The amount to format
     * @param string $currency The currency symbol (default: '')
     * @param bool $abbreviated Whether to use abbreviated format (K, M, B, T)
     * @return string The formatted currency
     */
    public static function formatCurrency($amount, string $currency = '', bool $abbreviated = false): string
    {
        if ($abbreviated) {
            return $currency . self::moneyfy($amount);
        }
        
        return $currency . number_format($amount, 2);
    }

    /**
     * Format percentage with proper decimal places
     * 
     * @param float $value The decimal value (e.g., 0.75 for 75%)
     * @param int $decimals Number of decimal places
     * @return string The formatted percentage
     */
    public static function formatPercentage(float $value, int $decimals = 2): string
    {
        return round($value * 100, $decimals) . '%';
    }
}