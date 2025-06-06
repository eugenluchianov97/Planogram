<?php

namespace App\Jobs;

use App\Models\Barcode;
use App\Models\Category;
use App\Models\Producer;
use App\Models\Product;
use App\Models\ProductImg;
use App\Models\ProductIntegration;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class ProductIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $telegram;

    private $id;

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('Begin Job');
        ini_set('memory_limit', '-1');


        $shop = Shop::where('c_id', $this->id)->first();
        $admins = User::select('telegram_chat_id', 'role')->whereRole('admin')->where('telegram_chat_id', '<>', NULL)->get();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            //CURLOPT_URL => "http://10.10.9.26:8080/fgm/hs/Planogramma/GetNomenclatures/{$this->id}",
            CURLOPT_URL => "http://192.168.102.254:7788/FGM_dev_6/hs/Planogramma/GetNomenclatures/{$this->id}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode("Exchange:Saturn")
            ),
        ));

        try {
            $response = curl_exec(handle: $curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            $data = json_decode($response);

            Log::info('got data');

            Log::info('code = '. $code);
            Log::info('er = '. curl_error($curl));


            //if ($code === 200 && !empty($data->Nomenclatures)) {
            if (!empty($data->Nomenclatures)) {
                Log::info('Data is not empty!');
                $data = array_chunk($data->Nomenclatures, 1000);
                foreach ($data as $products) {
                    foreach ($products as $item) {
                        $category = null;
                        $producer = null;
                        if (!empty(trim($item->NomenclatureGroup))) {
                            $category = Category::updateOrCreate(['name' => trim($item->NomenclatureGroup)], ['name' => trim($item->NomenclatureGroup)]);
                        }

                        if (!empty(trim($item->Producer))) {
                            $producer = Producer::updateOrCreate(['name' => trim($item->Producer)], ['name' => trim($item->Producer)]);


                            $product = Product::where(['Code' => $item->Code, 'shop_id' => $this->id,])->first();

                            $GroupKey = null;

                            if(isset($item->CommodityGroup_guid)) {
                                $GroupKey =  $item->CommodityGroup_guid == '00000000-0000-0000-0000-000000000000' ? null : $item->CommodityGroup_guid;
                            }
                            $data = [
                                'Code' => $item->Code,
                                'GroupKey' => $GroupKey,
                                'ScanerCode' => $item->ScanerCode,
                                'Name' => $item->Name,
                                'FullDescription' => $item->FullDescription,
                                'Price' => floatval(str_replace(',', '.', $item->Price)),


                                'Purchase_price' => floatval(str_replace(',', '.', $item->Purchase_price)),
                                'Margin' => floatval(str_replace(',', '.', $item->Margin)),
                                'Margin_rate' => floatval(str_replace(',', '.', $item->Margin_rate)),
                                'Producer' => $item->Producer,
                                'category_id' => isset($category->id) ? $category->id : null,
                                'producer_id' => isset($producer->id) ? $producer->id : null,
                                'shop_id' => $this->id,
                            ];

                            if(($product !== null && !$product->UpdatedManually) || $product == null){
                                $data['BoxHeight'] = floatval(str_replace(',', '.', $item->InaltimeBuc));
                                $data['BoxWidth'] = floatval(str_replace(',', '.', $item->LatimeBuc));
                                $data['BoxDepth'] = floatval(str_replace(',', '.', $item->LungimeBuc));

                                if($data['BoxHeight'] == 0 || $data['BoxWidth'] == 0 || $data['BoxDepth'] == 0){
                                    $data['BoxHeight'] = ($data['BoxHeight'] == 0) ? 10 : $data['BoxHeight'];
                                    $data['BoxWidth'] = ($data['BoxWidth'] == 0) ? 10 : $data['BoxWidth'];
                                    $data['BoxDepth'] = ($data['BoxDepth'] == 0) ? 10 : $data['BoxDepth'];
                                    $data['EmptyDimensions'] = true;
                                }
                            }

                            $product = Product::updateOrcreate(['Code' => $item->Code, 'shop_id' => $this->id,],$data);

                            Barcode::updateOrcreate([
                                'code' => $item->Code,
                                'product_id' => $product->id,
                                'shop_id' => $product->id,
                            ],
                                [
                                    'code' => $item->Code,
                                    'product_id' => $product->id,
                                    'img' => '/public/storage/pictures/'.$item->Code.'.PNG',
                                    'exists' => Barcode::exists($item->Code),
                                ]);
                            foreach($item->ScanerCodes as $scanerCode){
                                  Barcode::updateOrcreate([
                                      'code' => $scanerCode,
                                      'product_id' => $product->id,
                                      ],
                                      [
                                          'code' => $scanerCode,
                                          'product_id' => $product->id,
                                          'img' => '/storage/pictures/'.$scanerCode.'.PNG',
                                          'exists' => Barcode::exists($scanerCode),
                                      ]);

                            }

                        }
                    }
                    sleep(5);
                }

                foreach ($admins as $admin) {
                    try {
                        $this->telegram->sendMessage([
                            'chat_id' => $admin->telegram_chat_id,
                            'text' => "Товары успешно обновлени\nТорговая точка: " . $shop->name,
                        ]);
                    } catch (\Throwable $th) {
                    }
                }

                Log::info('end Job');
            }
            else {
                Log::info('error1');
                foreach ($admins as $admin) {
                    try {



                    } catch (\Throwable $th) {
                    }
                }
            }



        } catch (\Throwable $th) {
            Log::info('error2');
            Log::info($th);
            foreach ($admins as $admin) {
                try {
                    $this->telegram->sendMessage([
                        'chat_id' => $admin->telegram_chat_id,
                        'text' => "Ошибка при добавление товаров\n" . $th->getMessage(),
                    ]);
                } catch (\Throwable $th) {
                }
            }
        }
        Log::info('end');
    }
}
