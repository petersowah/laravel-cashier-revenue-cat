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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->morphs('billable');
            $table->string('transaction_id')->unique();
            $table->string('store');
            $table->string('environment');
            $table->decimal('price', 10, 2);
            $table->string('currency');
            $table->timestamp('purchase_date');
            $table->timestamp('expiration_date')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['billable_type', 'billable_id']);
            $table->index('transaction_id');
            $table->index('purchase_date');
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
