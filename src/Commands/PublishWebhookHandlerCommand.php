<?php

namespace PeterSowah\LaravelCashierRevenueCat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishWebhookHandlerCommand extends Command
{
    protected $signature = 'cashier-revenue-cat:publish-webhook-handler';

    protected $description = 'Publish the RevenueCat webhook handler and controller files';

    public function handle(): void
    {
        $this->publishWebhookHandler();
        $this->publishWebhookController();
        $this->updateConfig();
    }

    protected function publishWebhookHandler(): void
    {
        $targetPath = app_path('Listeners/HandleRevenueCatWebhook.php');

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook handler file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        $content = <<<'PHP'
<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;

class HandleRevenueCatWebhook
{
    public function handle(WebhookReceived $event): void
    {
        $payload = $event->payload;
        $type = $payload['event']['type'];

        Log::info('RevenueCat webhook received', [
            'type' => $type,
            'payload' => $payload,
        ]);

        switch ($type) {
            case 'INITIAL_PURCHASE':
                $this->handleInitialPurchase($payload);
                break;
            case 'RENEWAL':
                $this->handleRenewal($payload);
                break;
            case 'CANCELLATION':
                $this->handleCancellation($payload);
                break;
            case 'NON_RENEWING_PURCHASE':
                $this->handleNonRenewingPurchase($payload);
                break;
            case 'SUBSCRIPTION_PAUSED':
                $this->handleSubscriptionPaused($payload);
                break;
            case 'SUBSCRIPTION_RESUMED':
                $this->handleSubscriptionResumed($payload);
                break;
            case 'PRODUCT_CHANGE':
                $this->handleProductChange($payload);
                break;
            case 'BILLING_ISSUE':
                $this->handleBillingIssue($payload);
                break;
            case 'REFUND':
                $this->handleRefund($payload);
                break;
            case 'SUBSCRIPTION_PERIOD_CHANGED':
                $this->handleSubscriptionPeriodChanged($payload);
                break;
        }
    }

    protected function handleInitialPurchase(array $payload): void
    {
        // Handle initial purchase
        Log::info('Handling initial purchase', ['payload' => $payload]);
    }

    protected function handleRenewal(array $payload): void
    {
        // Handle renewal
        Log::info('Handling renewal', ['payload' => $payload]);
    }

    protected function handleCancellation(array $payload): void
    {
        // Handle cancellation
        Log::info('Handling cancellation', ['payload' => $payload]);
    }

    protected function handleNonRenewingPurchase(array $payload): void
    {
        // Handle non-renewing purchase
        Log::info('Handling non-renewing purchase', ['payload' => $payload]);
    }

    protected function handleSubscriptionPaused(array $payload): void
    {
        // Handle subscription paused
        Log::info('Handling subscription paused', ['payload' => $payload]);
    }

    protected function handleSubscriptionResumed(array $payload): void
    {
        // Handle subscription resumed
        Log::info('Handling subscription resumed', ['payload' => $payload]);
    }

    protected function handleProductChange(array $payload): void
    {
        // Handle product change
        Log::info('Handling product change', ['payload' => $payload]);
    }

    protected function handleBillingIssue(array $payload): void
    {
        // Handle billing issue
        Log::info('Handling billing issue', ['payload' => $payload]);
    }

    protected function handleRefund(array $payload): void
    {
        // Handle refund
        Log::info('Handling refund', ['payload' => $payload]);
    }

    protected function handleSubscriptionPeriodChanged(array $payload): void
    {
        // Handle subscription period changed
        Log::info('Handling subscription period changed', ['payload' => $payload]);
    }
}
PHP;

        File::put($targetPath, $content);
        $this->info('Webhook handler published successfully!');
    }

    protected function publishWebhookController(): void
    {
        $targetPath = app_path('Http/Controllers/RevenueCat/WebhookController.php');

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook controller file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        $content = <<<'PHP'
<?php

namespace App\Http\Controllers\RevenueCat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PeterSowah\LaravelCashierRevenueCat\Events\WebhookReceived;
use Symfony\Component\HttpKernel\Exception\HttpException;

class WebhookController
{
    /**
     * Handle incoming RevenueCat webhook requests.
     */
    public function handleWebhook(Request $request): \Illuminate\Http\Response
    {
        $payload = $request->all();

        // Log the incoming webhook request
        Log::info('RevenueCat webhook received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'event_type' => $payload['event']['type'] ?? 'unknown',
            'event_id' => $payload['event']['id'] ?? null,
        ]);

        $authHeader = $request->header('Authorization');

        if (! $authHeader) {
            Log::warning('RevenueCat webhook authorization header missing', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            throw new HttpException(401, 'Missing authorization header');
        }

        // Verify the authorization header
        $expectedAuth = 'Bearer '.config('cashier-revenue-cat.webhook.secret');
        if ($authHeader !== $expectedAuth) {
            Log::warning('RevenueCat webhook authorization header invalid', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'received' => $authHeader,
                'expected' => $expectedAuth,
            ]);
            throw new HttpException(401, 'Invalid authorization header');
        }

        // Log successful webhook processing
        Log::info('RevenueCat webhook processed successfully', [
            'event_type' => $payload['event']['type'] ?? 'unknown',
            'event_id' => $payload['event']['id'] ?? null,
            'subscriber_id' => $payload['event']['subscriber']['id'] ?? null,
            'product_id' => $payload['event']['product']['id'] ?? null,
        ]);

        event(new WebhookReceived($payload));

        return response('', 200);
    }
}
PHP;

        File::put($targetPath, $content);
        $this->info('Webhook controller published successfully!');
    }

    protected function updateConfig(): void
    {
        $configPath = config_path('cashier-revenue-cat.php');
        
        if (! File::exists($configPath)) {
            $this->warn('Configuration file not found. Please publish the configuration first using:');
            $this->line('php artisan vendor:publish --tag=cashier-revenue-cat-config');
            return;
        }

        $config = File::get($configPath);
        $config = str_replace(
            '\\PeterSowah\\LaravelCashierRevenueCat\\Http\\Controllers\\WebhookController::class . \'@handleWebhook\'',
            '\\App\\Http\\Controllers\\RevenueCat\\WebhookController::class . \'@handleWebhook\'',
            $config
        );
        
        File::put($configPath, $config);
        $this->info('Configuration updated to use the published controller!');

        // Clear route cache
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->info('Route cache cleared!');
    }
}
