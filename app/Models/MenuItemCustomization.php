<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemCustomization extends Model
{
    protected $fillable = [
        'menu_item_id',
        'ingredient_id', 
        'inventory_id',
        'name',
        'quantity', 
        'price', 
        'unit_of_measurement_id',
        'action'
    ];

    public function menuItem(){
        return $this->belongsTo(
            MenuItem::class,
            'menu_item_id',
            'id'
        );
    }

    public function ingredient(){
        return $this->belongsTo(
            Ingredient::class,
            'ingredient_id',
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
}
