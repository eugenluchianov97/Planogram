<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelves extends Model
{
    use HasFactory;

    protected $fillable = [
        'showcase_id',
        'position',
        'distance',
        'shelf_depth'
    ];

    public $timestamps = false;

    public function products () {
        return $this->belongsToMany(Product::class)
            ->select('products.*', 'product_shelves.deg as deg');
    }
}
