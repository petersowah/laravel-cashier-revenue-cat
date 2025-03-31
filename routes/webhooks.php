<?php

Route::post($endpoint, [WebhookController::class, 'handleWebhook'])
    ->name('cashier-revenue-cat.webhook');