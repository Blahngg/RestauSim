<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'menu_item_id',
        'inventory_id',
        'quantity',
        'unit_of_measurement_id',
    ];

    public function menuItem(){
        return $this->belongsTo(
            MenuItem::class,
            'menu_item_id',
            'id'
        );
    }

    public function inventory(){
        return $this->belongsTo(
            Inventory::class,
            'inventory_id',
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

    public function customizations(){
        return $this->hasMany(
            MenuItemCustomization::class,
            'ingredient_id',
            'id'
        );
    }
}
