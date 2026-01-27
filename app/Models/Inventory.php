<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'image',
        'opening_quantity',
        'quantity_on_hand',
        'unit_cost',
        'par_level',
        'inventory_category_id',
        'inventory_unit_id',
        'cost_unit_id',
    ];

    protected $appends = [
        'cost_per_unit'
    ];

    public function getCostPerUnitAttribute(){
        return $this->unit_cost . '/' . $this->costUnit->symbol;
    }

    public function category(){
        return $this->belongsTo(
            InventoryCategory::class,
            'inventory_category_id',
            'id'
        );
    }

    public function inventoryUnit(){
        return $this->belongsTo(
            UnitOfMeasurement::class,
            'inventory_unit_id',
            'id'
        );
    }
    public function costUnit(){
        return $this->belongsTo(
            UnitOfMeasurement::class,
            'cost_unit_id',
            'id'
        );
    }
}
