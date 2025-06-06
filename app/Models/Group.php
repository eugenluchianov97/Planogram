<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $guarded = false;

    protected $table = 'groups';


    public function parent()
    {
        return $this->belongsTo(self::class, 'Parent_Key','Key');
    }

    public function allParent()
    {
        return $this->parent()->with('allParent');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'Parent_Key','Key');
    }


    public static function recursion($groups,&$array = []){
        foreach ($groups as $groupItem){
            $array[] = $groupItem['Key'];
            if(count($groupItem->allChildren) > 0){
                self::recursion($groupItem->allChildren,$array);
            }
        }

        return $array;
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'GroupKey','Key');
    }



}
