<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIntegration extends Model
{
    use HasFactory;

    public $guarded = [];
    public $timestamps = false;

    public function shop() {
        return $this->belongsTo(Shop::class);
    }
}
