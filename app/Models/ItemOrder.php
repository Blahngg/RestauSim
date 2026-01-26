<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $fillable = [
        'order_id', 
        'menu_item_id', 
        'status',
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

    public function calculateUnitPrice(){
        $basePrice = $this->item->price;

        $quantity = $this->item->quantity;
        
        $customizationTotal = $this->customizations->sum(
            fn ($custom) => $custom->customization->price * $custom->quantity
        );

        return $$basePrice + $customizationTotal;
    }

    public function calculateTotalPrice(){
        return $this->calculateUnitPrice() * $this->quantity;
    }

    public function getComputedPrice(){
        return $this->calculateTotalPrice();
    }
}
