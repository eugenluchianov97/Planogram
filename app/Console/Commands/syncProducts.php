<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class syncProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-products {page} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ini_set('max_execution_time', 0);

        $page = $this->argument('page');
        $id = $this->argument('id');

        if($id == 666){
            sleep(20);
        }
        $per_page = 10;
        $skip = ($page -1 ) * $per_page;
        $products = Category::all()->skip($skip)->take($per_page);

        if(count($products) > 0) {
            sleep(2);
            Log::info($products);
            Log::info('------------page = '.$page.'-- id = '.$id.'-----------------');

            $nextPage = $page+1;

            $this->info(' Done. ' . "\n");

            Artisan::call('app:sync-products', [
                'page' => $nextPage,
                'id' => $id
            ]);
            $this->info('Sync done.');
        }

    }
}
