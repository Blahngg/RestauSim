<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'code',
        'name', 
        'description', 
        'base_price', 
        'image', 
        'vat_rate',
        'is_vat_inclusive',
        'menu_item_category_id'
    ];

    public function category(){
        return $this->belongsTo(
            MenuItemCategory::class,
            'menu_item_category_id',
            'id'
        );
    }

    public function ingredients(){
        return $this->hasMany(
            Ingredient::class,
            'menu_item_id',
            'id'
        );
    }

    public function customizations(){
        return $this->hasMany(
            MenuItemCustomization::class,
            'menu_item_id',
            'id'
        );
    }
}
