<?php

namespace PeterSowah\LaravelCashierRevenueCat\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class PublishWebhookHandlerCommand extends Command
{
    protected $signature = 'cashier-revenue-cat:publish-webhook-handler';

    protected $description = 'Publish the RevenueCat webhook handler file';

    public function handle(): void
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

        File::copy($sourcePath, $targetPath);
        $this->info('Webhook handler published successfully!');
    }
}
