<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
class AlphaVantageService
{
    protected string $baseUrl;
    protected string $apiKey;

    /**
     *
     */
    public function __construct()
    {
        $this->baseUrl = config('services.alpha_vantage.base_url');
        $this->apiKey = config('services.alpha_vantage.api_key');
    }

    /**
     * Fetch real-time stock price data for a given symbol.
     * Caches the result for 1 minute to reduce API calls.
     *
     * @param string $symbol
     * @return array|null
     */
    public function fetchStockPrices(string $symbol): ?array
    {
        // Cache key for stock data
        $cacheKey = "stock_price_{$symbol}";

        // Check if data exists in the cache
        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            return $cachedData; // Return cached data
        }

        try {
            $response = Http::retry(3, 1000)->get($this->baseUrl . 'query', [
                'function' => 'TIME_SERIES_INTRADAY',
                'symbol' => $symbol,
                'interval' => '1min',
                'apikey' => $this->apiKey,
            ]);
            if ($response->ok()) {
                $data = $response->json();

                if (isset($data['Time Series (1min)'])) {
                    $timeSeries = collect($data['Time Series (1min)'])->map(function ($values, $timestamp) {
                        return [
                            'timestamp' => $timestamp,
                            'open' => $values['1. open'],
                            'high' => $values['2. high'],
                            'low' => $values['3. low'],
                            'close' => $values['4. close'],
                            'volume' => $values['5. volume'] ?? null,
                        ];
                    })->values()->toArray();

                    Cache::put($cacheKey, $timeSeries, now()->addMinutes(1)); // Cache the data

                    return $timeSeries;
                }
            }
            Log::warning("No time-series data available for {$symbol}");
        } catch (\Exception $e) {
            Log::error("Error fetching data for {$symbol}: " . $e->getMessage());
        }

        return null;
    }
    public function storeStockPrices(array $stockData): void
    {
        $batchInsertData = [];

        foreach ($stockData as $stock) {
            $stockId = Stock::where('symbol', $stock['symbol'])->value('id'); // Get stock ID

            if ($stockId) {
                foreach ($stock['time_series'] as $timeSeries) {
                    $batchInsertData[] = [
                        'stock_id' => $stockId,
                        'open' => $timeSeries['open'],
                        'high' => $timeSeries['high'],
                        'low' => $timeSeries['low'],
                        'close' => $timeSeries['close'],
                        'volume' => $timeSeries['volume'],
                        'timestamp' => $timeSeries['timestamp'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Perform batch insert
        StockPrice::upsert(
            $batchInsertData,
            ['stock_id', 'timestamp'], // Unique columns for conflict
            ['open', 'high', 'low', 'close', 'volume', 'updated_at'] // Columns to update
        );
    }

    public function getLatestCachedPrice(string $symbol): ?array
    {
        return Cache::get("stock_price_{$symbol}");
    }
}
