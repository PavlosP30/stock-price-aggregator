<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade'); // Reference to stocks table
            $table->decimal('open', 12, 4)->nullable(false); // Opening price
            $table->decimal('high', 12, 4)->nullable(false); // Highest price
            $table->decimal('low', 12, 4)->nullable(false); // Lowest price
            $table->decimal('close', 12, 4)->nullable(false); // Closing price
            $table->bigInteger('volume')->nullable(); // Volume of shares traded
            $table->timestamp('timestamp')->index(); // The time the data was recorded
            $table->timestamps(); // created_at and updated_at

            // Composite index for optimized queries
            $table->index(['stock_id', 'timestamp']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
