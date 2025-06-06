<?php

namespace App\Console\Commands;

use App\Models\ArchivesIntegrations;
use App\Models\ProductArchives;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncArchives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-archives {shop} {year} {month}';

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

        $shop = $this->argument('shop');
        $year = $this->argument('year');
        $month = $this->argument('month');

        $data = $this->getArchives($shop,$year,$month);



        if($data['code'] === 200){

            Log::info(count($data['data']));
            foreach($data['data'] as $item){

                $match = [
                    'code' => $item['NomenclatureCode'],
                    'shop' => $item['Warehouse']['Warehouse_Number'],
                    'group' => $item['NomenclatureCommodityGroup']['Grup_GUID'],
                    'year' => $year,
                    'month' => $month,
                ];
                $arr = [
                    'code' => $item['NomenclatureCode'],
                    'shop' => $item['Warehouse']['Warehouse_Number'],
                    'group' => $item['NomenclatureCommodityGroup']['Grup_GUID'],
                    'year' => $year,
                    'month' => $month,
                    'PretAchizitie' =>$item['PretAchizitie'],
                    'PretVanzare' =>$item['PretVanzare'],
                    'Profit' =>$item['Profit'],
                    'Adaos' =>$item['Adaos'],
                    'Marja' =>$item['Marja'],
                    'Discount' =>$item['Discount'],
                    'DiscountLei' =>$item['DiscountLei'],
                    'TotalMarjaDoscount' =>$item['TotalMarjaDoscount'],
                    'ProfitInclDiscountLei' =>$item['ProfitInclDiscountLei'],
                    'PretPromo' =>$item['PretPromo'],
                    'PercentDifPretPromo' =>$item['PercentDifPretPromo'],
                    'RetroToMDL' =>$item['RetroToMDL'],
                    'QuantityPromo' =>$item['QuantityPromo'],
                    'CantitateVanzare' =>$item['CantitateVanzare'],
                    'SumaVanzare' =>$item['SumaVanzare'],
                ];
                ProductArchives::firstOrCreate($match,$arr);
            }

            ArchivesIntegrations::firstOrCreate([
                'shop' => $shop,
                'year' => $year,
                'month' => $month
            ]);

            Log::info('end sync
            
            ');
        }
        else {
            Log::info('not 200');
        }

    }

    public function getArchives($shop,$year,$month): array
    {

        $result =  [
            'code' => 500,
        ];
       try {
           $startDate = $this->getDate($year,$month);
           $endDate = $this->getDate($year,$month, true);

           $curl = curl_init();

           $url = "http://142.132.250.161:7788/FGM_dev_6/hs/Planogramma/GetPrices?StartDate=$startDate&EndDate=$endDate&Warehouse=$shop";

           curl_setopt_array($curl, array(
               CURLOPT_URL => $url,
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





           $response = curl_exec($curl);

           $data = json_decode($response);

           $code =  curl_getinfo($curl, CURLINFO_HTTP_CODE);

           Log::info('code = '.$code);
           Log::info('code = '.curl_error($curl));

           curl_close($curl);


           $result['code'] = $code;
           $result['data'] = $array = json_decode(json_encode($data->Data), true);
       }
       catch (\Exception $e){

           $result['message'] = $e;
       }


       return $result;
    }

    public function getDate($year,$month, $endDate = false){

        $day = '01';

        if($month < 1) $month = 1;
        if($month > 12 ) $month = 12;



        if($endDate) {
            if($month + 1 > 12) {
                $month = 1;
                $year = $year+1;
            }
        }
        $month = ($month < 10) ? '0'.$month : $month;

        return $year.$month.$day;
    }
}
