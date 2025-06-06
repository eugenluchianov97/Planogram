<?php

namespace App\Jobs;

use App\Models\Shop;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProductSyncJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //queue:work


        Artisan::call('app:sync-products', [
            'page' => 1,
            'id' => 666
        ]);

        Artisan::call('app:sync-products', [
            'page' => 1,
            'id' => 777
        ]);
    }
}
