<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloorPlan extends Model
{
    protected $fillable = [
        'name',
        'filepath'
    ];

    public function tables(){
        return $this->hasMany(
            Table::class,
            'floor_plan_id',
            'id'
        );
    }
}
