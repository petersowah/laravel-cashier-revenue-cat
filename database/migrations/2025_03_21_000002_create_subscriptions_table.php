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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->morphs('billable');
            $table->string('revenuecat_id')->unique();
            $table->string('name');
            $table->string('product_id');
            $table->string('price_id');
            $table->string('status');
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('trial_start')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Only create indexes if they don't exist
            if (! Schema::hasTable('subscriptions') || ! Schema::hasColumn('subscriptions', 'billable_type')) {
                $table->index(['billable_type', 'billable_id']);
            }
            $table->index('status');
            $table->index('current_period_end');
            $table->index('trial_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
