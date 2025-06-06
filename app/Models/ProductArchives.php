<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductArchives extends Model
{
    use HasFactory;

    protected $table = 'product_archives';

    protected $guarded = false;


    public function product(){
        return $this->hasMany(Product::class,'Code','code');
    }




}
