<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Services\AlphaVantageService;

class StockReportController extends Controller
{
    protected AlphaVantageService $alphaVantageService;

    public function __construct(AlphaVantageService $alphaVantageService)
    {
        $this->alphaVantageService = $alphaVantageService;
    }

    /**
     * Return time-series data for all stocks.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $stocks = Stock::with(['prices' => function ($query) {
            $query->orderBy('timestamp', 'desc');
        }])->get();

        $data = $stocks->map(function ($stock) {
            $timeSeries = $stock->prices->map(function ($price, $index) use ($stock) {
                $previousPrice = $stock->prices->get($index + 1);

                return [
                    'timestamp' => $price->timestamp,
                    'open' => $price->open,
                    'high' => $price->high,
                    'low' => $price->low,
                    'close' => $price->close,
                    'volume' => $price->volume,
                    'percentage_change' => calculatePercentageChange($price, $previousPrice), // Use the helper
                ];
            });

            return [
                'symbol' => $stock->symbol,
                'name' => $stock->name,
                'time_series' => $timeSeries,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
    /**
     * Return time-series data for a specific stock.
     *
     * @param string $symbol
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($symbol)
    {
        $stock = Stock::with(['prices' => function ($query) {
            $query->orderBy('timestamp', 'desc'); // Get the latest prices
        }])->where('symbol', $symbol)->first();

        if (!$stock) {
            return response()->json([
                'success' => false,
                'message' => "Stock with symbol {$symbol} not found.",
            ], 404);
        }

        $data = $stock->prices->map(function ($price, $index) use ($stock) {
            $previousPrice = $stock->prices->get($index + 1);

            return [
                'timestamp' => $price->timestamp,
                'open' => $price->open,
                'high' => $price->high,
                'low' => $price->low,
                'close' => $price->close,
                'volume' => $price->volume,
                'percentage_change' => $this->calculatePercentageChange($price, $previousPrice),
            ];
        });

        return response()->json([
            'success' => true,
            'stock' => [
                'symbol' => $stock->symbol,
                'name' => $stock->name,
            ],
            'data' => $data,
        ]);
    }

    /**
     * Get the latest cached stock price.
     *
     * @param string $symbol
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLatestCachedPrice(string $symbol)
    {
        $data = $this->alphaVantageService->getLatestCachedPrice($symbol);

        if ($data) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => "No cached data found for symbol: {$symbol}",
        ], 404);
    }
}
