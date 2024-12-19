<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AlphaVantageService;
use App\Models\Stock;
use Illuminate\Support\Facades\Log;

class FetchStockPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stocks:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch stock prices from Alpha Vantage API and store them in the database.';

    protected AlphaVantageService $alphaVantageService;

    /**
     * Create a new command instance.
     */
    public function __construct(AlphaVantageService $alphaVantageService)
    {
        parent::__construct();
        $this->alphaVantageService = $alphaVantageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching stock prices...');

        // Fetch all active stocks
        $stocks = Stock::all();
        if ($stocks->isEmpty()) {
            $this->warn('No stocks found in the database.');
            return;
        }

        $stockData = []; // To hold all data for batch insertion

        foreach ($stocks as $stock) {
            try {
                $this->info("Fetching data for {$stock->symbol}...");
                $timeSeriesData = $this->alphaVantageService->fetchStockPrices($stock->symbol);

                if ($timeSeriesData) {
                    $stockData[] = [
                        'symbol' => $stock->symbol,
                        'time_series' => $timeSeriesData,
                    ];
                    $this->info("Data fetched for {$stock->symbol}.");
                } else {
                    $this->warn("No data available for {$stock->symbol}.");
                }
            } catch (\Exception $e) {
                Log::error("Error fetching stock data for {$stock->symbol}: " . $e->getMessage());
                $this->error("Failed to fetch data for {$stock->symbol}.");
            }
        }

        // Store all fetched data in a batch insert
        if (!empty($stockData)) {
            $this->info('Storing stock prices in the database...');
            $this->alphaVantageService->storeStockPrices($stockData);
            $this->info('Stock prices stored successfully.');
        } else {
            $this->warn('No stock data to store.');
        }

        $this->info('Stock price fetching process completed.');
    }
}
