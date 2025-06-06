<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function getPrices(Request $request){

        $shop_id = $request->shop_id;
        $nomenclature_group = $request->nomenclature_group;
        $from = $request->from;
        $to = $request->to;

        ini_set('memory_limit', '-1');
        $curl = curl_init();
        $url = 'http://192.168.102.254:7788/FGM_dev_6/hs/Planogramma/GetPrices?StartDate='.$from.'&EndDate='.$to.'&Warehouse='.$shop_id.'&NomenclatureGroup='.$nomenclature_group;
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

        try {
            $response = curl_exec(handle: $curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return $response;
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
