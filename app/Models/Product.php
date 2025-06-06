<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $appends = [
        'updated',
    ];


    public function getUpdatedAttribute() {
        return empty($this->updated_at) ? 0 : $this->updated_at->getTimestampMs();
    }

    public function shop() {
        return $this->belongsTo(Shop::class,'shop_id','c_id');
    }

    public function group() {
        return $this->belongsTo(Group::class, 'GroupKey','Key');
    }
    public function allGroups() {
        return $this->group()->with('allParent');
    }

    public function allGroupsChildren() {
        return $this->group()->with('allChildren');
    }


    public function barcodes()
    {
        return $this->hasMany(Barcode::class);
    }

    public function archives(){
        return $this->hasMany(ProductArchives::class,'code','Code');
    }

    public function last(){
        return $this->hasMany(ProductArchives::class,'code','Code');
    }

    public function setImages($file){
        Storage::disk('public')->put('pictures/'.$this->Code.'.PNG', file_get_contents($file));
        Barcode::updateOrcreate(['code' => $this->Code, 'product_id' => $this->id] ,
            [
                'code' => $this->Code,
                'product_id' => $this->id,
                'img' => '/storage/pictures/'.$this->Code.'.PNG',
                'exists' => Barcode::exists($this->Code),
            ]);

        if(count($this->barcodes) > 0){
             foreach($this->barcodes as $barcode){
                 Storage::disk('public')->put('pictures/'.$barcode->code.'.PNG', file_get_contents($file));
                 Barcode::updateOrcreate(['code' => $barcode->code, 'product_id' => $this->id],
                     [
                         'code' => $this->Code,
                         'product_id' => $this->id,
                         'img' => '/storage/pictures/'.$barcode->code.'.PNG',
                         'exists' => Barcode::exists($barcode->code),
                     ]);
             }
        }
    }
}
