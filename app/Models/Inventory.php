<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'image',
        'quantity',
        'price',
        'par_level',
        'inventory_category_id',
        'unit_of_measurement_id',
    ];

    public function category(){
        return $this->belongsTo(
            InventoryCategory::class,
            'inventory_category_id',
            'id'
        );
    }

    public function unitOfMeasurement(){
        return $this->belongsTo(
            UnitOfMeasurement::class,
            'unit_of_measurement_id',
            'id'
        );
    }
}
