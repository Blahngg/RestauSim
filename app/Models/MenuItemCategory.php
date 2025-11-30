<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemCategory extends Model
{
    protected $fillable = [
        'name'
    ];

    public function menuItems(){
        return $this->hasMany(
            MenuItem::class,
            'menu_item_category_id',
            'id'
        );
    }
}
