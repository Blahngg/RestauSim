<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $fillable = [
        'order_id', 
        'menu_item_id', 
        'quantity', 
    ];

    public function order(){
        return $this->belongsTo(
            Order::class,
            'order_id',
            'id'
        );
    }

    public function item(){
        return $this->belongsTo(
            MenuItem::class,
            'menu_item_id',
            'id'
        );
    }

    public function customizations(){
        return $this->hasMany(
            ItemOrderCustomization::class,
            'item_order_id',
            'id'
        );
    }
}
