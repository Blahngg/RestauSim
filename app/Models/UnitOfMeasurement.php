<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasurement extends Model
{
    protected $fillable = [
        'nmae',
        'symbol'
    ];

    public function inventoryItems(){
        return $this->hasMany(
            Inventory::class,
            'inventory_unit_id',
            'id'
        );
    }

    public function costUnitItems(){
        return $this->hasMany(
            Inventory::class,
            'cost_unit_id',
            'id'
        );
    }
}
