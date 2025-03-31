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
        Schema::create('subscriptions', static function (Blueprint $table) {
            $table->id();
            $table->morphs('billable');
            $table->string('name');
            $table->string('product_id');
            $table->string('currency');
            $table->string('price');
            $table->string('status');
            $table->string('store');
            $table->timestamp('current_period_started_at')->nullable();
            $table->timestamp('current_period_ended_at')->nullable();
            $table->timestamp('trial_started_at')->nullable();
            $table->timestamp('trial_ended_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Only create indexes if they don't exist
            // if (!Schema::hasTable('subscriptions') || !Schema::hasColumn('subscriptions', 'billable_type')) {
            //     $table->index(['billable_type', 'billable_id']);
            // }
            $table->index('current_period_ended_at');
            $table->index('trial_ended_at');
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
