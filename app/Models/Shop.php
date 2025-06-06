<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'c_id'
    ];

    protected $appends = ['updated'];

    public function getUpdatedAttribute() {
        $productUpdated = ProductIntegration::where('shop_id', $this->id)->where('last_updated_at', '>=', Carbon::now()->startOfDay())->exists();
        
        return $productUpdated;
    }

    public function showcases () {
        if (auth()->user()->role == 'user') {
            return $this->hasMany(Showcase::class)->whereIn('id', collect(auth()->user()->showcases)->map(fn($showcase) => $showcase->id)->toArray());
        }
        return $this->hasMany(Showcase::class);
    }


    public function products () {
        return $this->belongsToMany(Product::class,'products','shop_id','c_id');
    }

    public static function boot(): void
    {
        parent::boot();
        static::created(fn (Model $model) =>
            ProductIntegration::create([
                'shop_id' => $model->id,
                'last_updated_at' => Carbon::now()->subDay(),
            ]),
        );
    }
}
