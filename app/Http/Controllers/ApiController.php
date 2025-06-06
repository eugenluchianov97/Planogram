<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Group;
use App\Models\Shop;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function index ($id) {
        ini_set('memory_limit', '-1');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://10.10.9.26:8080/fgm/hs/Planogramma/GetNomenclatures/$id",
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
        
        return response()->json([
            'data' => json_decode($response),
            'status' => $code,
        ]);
       } catch (\Throwable $th) {
            dd($th->getMessage());
       }
    }

    public function getShops(){
        ini_set('memory_limit', '-1');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://10.10.9.26:8080/fgm/hs/Planogramma/GetWarehouses/",
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
            foreach ($data->Warehouses as $shopItem){
                $shop = Shop::where('c_id',$shopItem->WarehouseNumber)->first();
                if(!$shop){
                    Shop::create([
                          'name' => $shopItem->Name,
                          'c_id' => $shopItem->WarehouseNumber,
                          'address' => '-'
                    ]);

                    dump('added');
                }
            }
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }

    public function getGroups(){
        //http://fgm.agg.int:7735/fgm/odata/standard.odata/Catalog_CommodityGroups?&$format=json&$select=Ref_Key,Parent_Key,Description

        ini_set('memory_limit', '-1');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://10.10.9.26:7735/fgm/odata/standard.odata/Catalog_CommodityGroups?&$format=json&$select=Ref_Key,Parent_Key,Description&$filter=DeletionMark%20eq%20false',
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


        foreach ($data->value as $groupItem){
            $group = Group::where('Key',$groupItem->Ref_Key)->first();

            if(!$group){
                $data = [
                    'Key' => $groupItem->Ref_Key,
                    'Description' => $groupItem->Description,
                    'Parent_Key' => null
                ];
                Group::create($data);

                dump('added');


            }
        }

        foreach ($data->value as $groupItem){
            $group = Group::where('Key',$groupItem->Ref_Key)->first();
            $groupParent = Group::where('Key',$groupItem->Parent_Key)->first();

            if($group && $groupParent){

                $group->Parent_Key = $groupParent->Key;
                $group->save();

                dump('updated');
            }
        }
        try {

        } catch (\Throwable $th) {}
    }

    public function syncBarcodes(Request $request){

        if($request->all == true){
            $barcodes = Barcode::all();
        }
        else {
            $barcodes = Barcode::where('exists', false)->get();
        }
        $start = microtime(true);

        DB::transaction(function() use($barcodes) {

            foreach($barcodes as $barcode){
                Barcode::where(['id' => $barcode->id])
                    ->update([
                        'exists' => Barcode::exists($barcode->code),
                        'img' => '/storage/pictures/'.$barcode->code.'.PNG'
                    ]);
            }
        });

        // Execute the query
        $time = microtime(true) - $start;

        return [
            'code' => 200,
            'status' => 'sync completed, updated '.$barcodes->count().' rows in '.$time.' sec.'
        ];
    }
}
