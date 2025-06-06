<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Showcase extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'width',
        'height',
        // 'shelf_depth',
        'comment',
        'shop_id',
    ];


    public function shelves() {
        return $this->hasMany(Shelves::class);
    }

    public function shop() {
        return $this->belongsTo(Shop::class);
    }
}
