<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'table_id',
        'type',
        'status',
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
}
