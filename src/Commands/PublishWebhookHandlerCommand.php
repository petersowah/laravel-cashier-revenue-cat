<?php

namespace PeterSowah\LaravelCashierRevenueCat\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\File;

class PublishWebhookHandlerCommand extends Command implements Isolatable
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
        $sourcePath = __DIR__.'/../Listeners/HandleRevenueCatWebhook.php';

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook handler file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        if (! File::exists($sourcePath)) {
            $this->error('Source webhook handler file not found. Please ensure the package is properly installed.');

            return;
        }

        // Read the source file content
        $content = File::get($sourcePath);

        // Update the namespace from package namespace to App\Listeners
        $content = str_replace(
            'namespace PeterSowah\LaravelCashierRevenueCat\Listeners;',
            'namespace App\Listeners;',
            $content
        );

        File::put($targetPath, $content);
        $this->info('Webhook handler published successfully!');
    }

    protected function publishWebhookController(): void
    {
        $targetPath = app_path('Http/Controllers/RevenueCat/RevenueCatWebhookController.php');
        $sourcePath = __DIR__.'/../Http/Controllers/RevenueCatWebhookController.php';

        if (! File::exists(dirname($targetPath))) {
            File::makeDirectory(dirname($targetPath), 0755, true);
        }

        if (File::exists($targetPath)) {
            if (! $this->confirm('The webhook controller file already exists. Do you want to overwrite it?')) {
                return;
            }
        }

        if (! File::exists($sourcePath)) {
            $this->error('Source webhook controller file not found. Please ensure the package is properly installed.');

            return;
        }

        // Read the source file content
        $content = File::get($sourcePath);

        // Update the namespace from package namespace to App\Http\Controllers\RevenueCat
        $content = str_replace(
            'namespace PeterSowah\LaravelCashierRevenueCat\Http\Controllers;',
            'namespace App\Http\Controllers\RevenueCat;',
            $content
        );

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
            '\\PeterSowah\\LaravelCashierRevenueCat\\Http\\Controllers\\RevenueCatWebhookController::class . \'@handleWebhook\'',
            '\\App\\Http\\Controllers\\RevenueCat\\RevenueCatWebhookController::class . \'@handleWebhook\'',
            $config
        );

        File::put($configPath, $config);
        $this->info('Configuration updated to use the published controller!');

        // Clear caches to ensure changes take effect
        $this->info('Clearing caches...');
        $this->call('config:clear');
        $this->info('Caches cleared successfully!');
    }
}
