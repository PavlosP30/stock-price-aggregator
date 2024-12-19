<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Get all stocks with their time-series data
Route::get('/reports/stocks', [StockReportController::class, 'index']);

// Get time-series data for a specific stock by symbol
Route::get('/reports/stocks/{symbol}', [StockReportController::class, 'show']);

// Get the latest cached price
Route::get('/stocks/{symbol}/latest', [StockReportController::class, 'getLatestCachedPrice']);
