<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __invoke($code) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://168.119.73.48:8080/fgm_o/hs/Planogramma/GetPicture/$code",
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

        return response()->json([
            'image' => $data->PictureBase64
        ]);
       } catch (\Throwable $th) {
            dd($th->getMessage());
       }
    }
}
