<?php

use App\Models\StockPrice;

/**
 * Calculate the percentage change between two stock prices.
 *
 * @param StockPrice|null $currentPrice
 * @param StockPrice|null $previousPrice
 * @return float|null
 */
if (!function_exists('calculatePercentageChange')) {
    function calculatePercentageChange(?StockPrice $currentPrice, ?StockPrice $previousPrice): ?float
    {
        if (!$currentPrice || !$previousPrice) {
            return null;
        }

        $currentClose = $currentPrice->close;
        $previousClose = $previousPrice->close;

        if ($previousClose == 0) {
            return null; // Avoid division by zero
        }

        return round((($currentClose - $previousClose) / $previousClose) * 100, 2);
    }
}
