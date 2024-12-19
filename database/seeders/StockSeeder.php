<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stocks = [
            ['symbol' => 'IBM', 'name' => 'IBM'],
            ['symbol' => 'AAPL', 'name' => 'Apple Inc.'],
            ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.'],
            ['symbol' => 'MSFT', 'name' => 'Microsoft Corporation'],
            ['symbol' => 'AMZN', 'name' => 'Amazon.com, Inc.'],
            ['symbol' => 'TSLA', 'name' => 'Tesla, Inc.'],
            ['symbol' => 'META', 'name' => 'Meta Platforms, Inc.'],
            ['symbol' => 'NFLX', 'name' => 'Netflix, Inc.'],
            ['symbol' => 'NVDA', 'name' => 'NVIDIA Corporation'],
            ['symbol' => 'DIS', 'name' => 'The Walt Disney Company'],
            ['symbol' => 'ADBE', 'name' => 'Adobe Inc.'],
        ];

        foreach ($stocks as $stock) {
            Stock::updateOrCreate(
                ['symbol' => $stock['symbol']], // Match by stock symbol
                ['name' => $stock['name']]     // Update or insert the stock name
            );
        }
    }
}
