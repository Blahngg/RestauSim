<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads;
    public $inventories = [
        [
            'name' => '',
            'code' => '',
            'image' => '',
            'quantity' => '',
            'price' => '',
            'par_level' => '',
            'inventory_category_id' => '',
            'unit_of_measurement_id' => '',
        ]
    ];
    public function add(){
        $this->inventories[] = [
            'name' => '',
            'code' => '',
            'image' => '',
            'quantity' => '',
            'price' => '',
            'par_level' => '',
            'inventory_category_id' => '',
            'unit_of_measurement_id' => '',
        ];
    }
    public function remove($index){
        unset($this->inventories[$index]);
        $this->inventories = array_values($this->inventories);
    }
    public function store(){
        $validated = $this->validate([
            'inventories.*.name' => 'required|string',
            'inventories.*.code' => 'required|unique:inventories.code',
            'inventories.*.image' => 'required|image',
            'inventories.*.quantity' => 'required|numeric|gt:0',
            'inventories.*.price' => 'required|numeric|gt:0',
            'inventories.*.par_level' => 'required|numeric|gt:0',
            'inventories.*.inventory_category_id' => 'required|exists:inventory_categories.id',
            'inventories.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements.id',
        ]);

        DB::beginTransaction();
        try{
            foreach($validated['inventories'] as $item){
                $item['image'] = $item['image']->store('inventory_images', 'public');
                Inventory::create($item);
            }
            DB::commit();
            $this->reset('inventories');
        }catch(Exception $e){
            dd($e);
            DB::rollBack();
        }
    }
    public function render()
    {
        $units = UnitOfMeasurement::all()->groupBy('category');
        $categories = InventoryCategory::all();
        
        return view('livewire.inventory.create')
            ->with(compact('units', 'categories'));
    }
}
