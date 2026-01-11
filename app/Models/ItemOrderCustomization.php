<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrderCustomization extends Model
{
    protected $fillable = [
        'item_order_id',
        'menu_item_customization_id',
        'quantity',
    ];

    public function item(){
        return $this->belongsTo(
            ItemOrder::class,
            'item_order_id',
            'id'
        );
    }

    public function customization(){
        return $this->belongsTo(
            MenuItemCustomization::class,
            'menu_item_customization_id',
            'id'
        );
    }
}
