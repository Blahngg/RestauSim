<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

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

    public function computeCostPerUnit(string $baseUnit, string $unitCategory){
        if($unitCategory === 'weight'){
            $mass = new Mass(1, $this->costUnit->symbol);
            $baseQuantity = $mass->toUnit($baseUnit);
        }
        elseif($unitCategory === 'volume'){
            $mass = new Volume(1, $this->costUnit->symbol);
            $baseQuantity = $mass->toUnit($baseUnit);
        }
        elseif($unitCategory === 'count'){
            return $this->unit_cost;
        }

        // dd($this->unit_cost, $baseQuantity, $this->unit_cost/$baseQuantity);

        return (int) $this->unit_cost / $baseQuantity;
        // return round((int) $this->unit_cost / $baseQuantity, 2);
    }

    // Relationships

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
