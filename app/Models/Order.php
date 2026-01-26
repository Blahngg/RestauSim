<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'code',
        'table_id',
        'type',
        'status',
        'subtotal',
        'service_charge',
        'discount_total',
        'vat_total',
        'grand_total',
    ];

    public function items(){
        return $this->hasMany(
            ItemOrder::class,
            'order_id',
            'id'
        );
    }

    public function table(){
        return $this->belongsTo(
            Table::class,
            'table_id',
            'id'
        );
    }

    public function calculateSubTotal(){
        return $this->items->sum(
            fn ($item) => $item->calculateTotalPrice()
        );
    }

    public function calculateTotal(){
        return $this->calculateSubTotal()
            ->$this->service_charge
            ->$this->tax;
    }

    public function getComputedTotalAttribute(){
        return $this->calculateTotal();
    }
}
