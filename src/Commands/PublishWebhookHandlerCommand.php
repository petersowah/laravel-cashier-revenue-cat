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
    }

    protected function publishWebhookHandler(): void
    {
        $sourcePath = __DIR__.'/../Listeners/HandleRevenueCatWebhook.php';
        $targetPath = app_path('Listeners/HandleRevenueCatWebhook.php');

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook handler file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        $content = File::get($sourcePath);
        $content = str_replace(
            'namespace PeterSowah\\LaravelCashierRevenueCat\\Listeners;',
            'namespace App\\Listeners;',
            $content
        );

        File::put($targetPath, $content);
        $this->info('Webhook handler published successfully!');
    }

    protected function publishWebhookController(): void
    {
        $sourcePath = __DIR__.'/../Http/Controllers/WebhookController.php';
        $targetPath = app_path('Http/Controllers/RevenueCat/WebhookController.php');

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook controller file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        $content = File::get($sourcePath);
        $content = str_replace(
            'namespace PeterSowah\\LaravelCashierRevenueCat\\Http\\Controllers;',
            'namespace App\\Http\\Controllers\\RevenueCat;',
            $content
        );

        File::put($targetPath, $content);
        $this->info('Webhook controller published successfully!');

        // Update the config to use the published controller
        $configPath = config_path('cashier-revenue-cat.php');
        if (File::exists($configPath)) {
            $config = File::get($configPath);
            $config = str_replace(
                '\\PeterSowah\\LaravelCashierRevenueCat\\Http\\Controllers\\WebhookController::class . \'@handleWebhook\'',
                '\\App\\Http\\Controllers\\RevenueCat\\WebhookController::class . \'@handleWebhook\'',
                $config
            );
            File::put($configPath, $config);
            $this->info('Configuration updated to use the published controller!');
        }

        // Clear route cache
        $this->call('route:clear');
        $this->call('config:clear');
        $this->call('cache:clear');
        $this->info('Route cache cleared!');
    }
}
