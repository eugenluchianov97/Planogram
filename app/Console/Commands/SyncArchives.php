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
            foreach($data['data'] as $item){

                $match = [
                    'code' => $item->NomenclatureCode,
                    'shop' => $item->Warehouse->Warehouse_Number,
                    'group' => $item->NomenclatureCommodityGroup->Grup_GUID,
                    'year' => $year,
                    'month' => $month,
                ];
                $arr = [
                    'code' => $item->NomenclatureCode,
                    'shop' => $item->Warehouse->Warehouse_Number,
                    'group' => $item->NomenclatureCommodityGroup->Grup_GUID,
                    'year' => $year,
                    'month' => $month,
                    'PretAchizitie' =>$item->PretAchizitie,
                    'PretVanzare' =>$item->PretVanzare,
                    'Profit' =>$item->Profit,
                    'Adaos' =>$item->Adaos,
                    'Marja' =>$item->Marja,
                    'Discount' =>$item->Discount,
                    'DiscountLei' =>$item->DiscountLei,
                    'TotalMarjaDoscount' =>$item->TotalMarjaDoscount,
                    'ProfitInclDiscountLei' =>$item->ProfitInclDiscountLei,
                    'PretPromo' =>$item->PretPromo,
                    'PercentDifPretPromo' =>$item->PercentDifPretPromo,
                    'RetroToMDL' =>$item->RetroToMDL,
                    'QuantityPromo' =>$item->QuantityPromo,
                    'CantitateVanzare' =>$item->CantitateVanzare,
                    'SumaVanzare' =>$item->SumaVanzare,
                ];
                ProductArchives::firstOrCreate($match,$arr);
            }

            ArchivesIntegrations::firstOrCreate([
                'shop' => $shop,
                'year' => $year,
                'month' => $month
            ]);
        }

        Log::info($data['code']);
    }

    public function getArchives($shop,$year,$month){
        //20240401
        $startDate = $this->getDate($year,$month);
        $endDate = $this->getDate($year,$month+1);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://192.168.102.254:7788/FGM_dev_6/hs/Planogramma/GetPrices?StartDate=".$startDate."&EndDate=".$endDate."&Warehouse=".$shop,
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

        $response = curl_exec(handle: $curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $data = json_decode($response);

        return [
            'code' =>$code,
            'data' => $data->Data
        ];
    }

    public function getDate($year,$month){
        $day = '01';
        $month = ($month < 10) ? '0'.$month : $month;

        return $year.$month.$day;
    }
}
