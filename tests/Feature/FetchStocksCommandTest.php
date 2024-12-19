<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\Stock;
use App\Models\StockPrice;

class FetchStocksCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the stocks:fetch command for a single stock.
     *
     * @return void
     */
    public function test_fetch_stocks_command_for_single_stock()
    {
        // Create a stock record
        $stock = Stock::create(['symbol' => 'IBM', 'name' => 'IBM']);

        // Debugging: Check if the stock was created
        $this->assertDatabaseHas('stocks', [
            'symbol' => 'IBM',
            'name' => 'IBM',
        ]);

        // Mock the API response
        Cache::shouldReceive('remember')
            ->with("stock_price_IBM", \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn([
                [
                    'timestamp' => now()->toDateTimeString(),
                    'open' => '144.2300',
                    'high' => '144.2500',
                    'low' => '144.2300',
                    'close' => '144.2500',
                    'volume' => 1200,
                ]
            ]);

        // Run the Artisan command
        $this->artisan('stocks:fetch')
            ->expectsOutput("Fetching data for IBM...")
            ->expectsOutput("Failed to fetch data for IBM.")
            ->assertExitCode(0);

        // Assert the stock price was saved to the database
        $this->assertDatabaseHas('stock_prices', [
            'stock_id' => $stock->id,
            'open' => '144.2300',
            'close' => '144.2500',
        ]);
    }

    /**
     * Test the stocks:fetch command for multiple stocks.
     *
     * @return void
     */
    public function test_fetch_stocks_command_for_multiple_stocks()
    {
        // Prepare test data
        $stocks = Stock::insert([
            ['symbol' => 'IBM', 'name' => 'IBM'],
            ['symbol' => 'AAPL', 'name' => 'Apple'],
        ]);

        // Mock the API response
        Cache::shouldReceive('remember')
            ->with("stock_price_IBM", \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn([
                [
                    'timestamp' => now()->toDateTimeString(),
                    'open' => '144.2300',
                    'high' => '144.2500',
                    'low' => '144.2300',
                    'close' => '144.2500',
                    'volume' => 1200,
                ]
            ]);

        Cache::shouldReceive('remember')
            ->with("stock_price_AAPL", \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn([
                [
                    'timestamp' => now()->toDateTimeString(),
                    'open' => '175.0000',
                    'high' => '176.0000',
                    'low' => '174.5000',
                    'close' => '175.5000',
                    'volume' => 100000,
                ]
            ]);

        // Run the Artisan command
        $this->artisan('stocks:fetch')
            ->expectsOutput("Fetching data for IBM...")
            ->expectsOutput("Data fetched and stored for IBM.")
            ->expectsOutput("Fetching data for AAPL...")
            ->expectsOutput("Data fetched and stored for AAPL.")
            ->assertExitCode(0);

        // Assert the stock prices were saved to the database
        $this->assertDatabaseHas('stock_prices', [
            'stock_id' => Stock::where('symbol', 'IBM')->first()->id,
            'open' => '144.2300',
            'close' => '144.2500',
        ]);

        $this->assertDatabaseHas('stock_prices', [
            'stock_id' => Stock::where('symbol', 'AAPL')->first()->id,
            'open' => '175.0000',
            'close' => '175.5000',
        ]);
    }
}
