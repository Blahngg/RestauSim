<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOfMeasurement extends Model
{
    protected $fillable = [
        'nmae',
        'symbol'
    ];

    public function inventories(){
        return $this->hasMany(
            Inventory::class,
            'unit_of_measurement_id',
            'id'
        );
    }
}
