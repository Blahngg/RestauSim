<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemOrder extends Model
{
    protected $fillable = [
        'order_id', 
        'menu_item_id', 
        'status',
        'quantity_ordered', 
        'price_at_sale', 
        'vat_rate', 
        'vat_removed_amount', 
        'discount_type', 
        'discount_percentage', 
        'discount_amount', 
        'vat_exempted_due_to_discount', 
        'net_amount', 
        'notes', 
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
