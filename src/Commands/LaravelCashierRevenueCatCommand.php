<?php

namespace PeterSowah\LaravelCashierRevenueCat\Commands;

use Illuminate\Console\Command;

class LaravelCashierRevenueCatCommand extends Command
{
    public $signature = 'laravel-cashier-revenue-cat';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
