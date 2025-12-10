<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'floor_plan_id',
        'table_code',
        'status',
        'svg_id',
    ];

    public function floorplan(){
        return $this->belongsTo(
            FloorPlan::class,
            'floor_plan_id',
            'id'
        );
    }
}
