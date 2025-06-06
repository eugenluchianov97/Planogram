<?php

namespace App\Models;

use App\Jobs\ProductSyncJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Barcode extends Model
{
    use HasFactory;

    public $guarded = [];

    public function products()
    {
        return $this->belongsTo(Product::class);
    }

    public static function exists($code){
        $url = public_path().'/storage/pictures/' . $code . '.PNG';

        if (file_exists($url)) {
            return true;
        }

        return false;

    }
}
