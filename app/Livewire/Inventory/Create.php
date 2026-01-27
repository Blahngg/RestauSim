<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithFileUploads;
    public $storedUnits;
    public $unitCategory;
    public $inventoryDataToSave = [
        'name' => '',
        'code' => '',
        'image' => '',
        'opening_quantity' => '',
        'opening_quantity_unit_id' => '',
        'cost' => '',
        'cost_unit_id' => '',
        'par_level' => '',
        'inventory_category_id' => '',
        'inventory_unit_id' => '',
    ];
    public function mount(){
        $this->storedUnits = UnitOfMeasurement::all();
    }
    // public function add(){
    //     $this->inventories[] = [
    //         'name' => '',
    //         'code' => '',
    //         'image' => '',
    //         'opening_quantity' => '',
    //         'opening_quantity_unit_id' => '',
    //         'cost' => '',
    //         'cost_unit_id' => '',
    //         'par_level' => '',
    //         'inventory_category_id' => '',
    //         'inventory_unit_id' => '',
    //     ];
    // }
    // public function remove($index){
    //     unset($this->inventories[$index]);
    //     $this->inventories = array_values($this->inventories);
    // }
    public function updatedInventoryDataToSaveInventoryUnitId($value){
        // dd($value);
        // dd($this->storedUnits);
        foreach($this->storedUnits as $unit){
            if($unit->id === (int) $value){
                $this->unitCategory = $unit->category;
            }
        }

        $this->inventoryDataToSave['opening_quantity_unit_id'] = '';
        $this->inventoryDataToSave['cost_unit_id'] = '';
    }
    public function store(){
        // dd($this->inventoryDataToSave);
        $validated = $this->validate([
            'inventoryDataToSave.name' => 'required|string',
            'inventoryDataToSave.code' => 'required|unique:inventories,code',
            'inventoryDataToSave.image' => 'required|image',
            'inventoryDataToSave.opening_quantity' => 'required|numeric|gt:0',
            'inventoryDataToSave.opening_quantity_unit_id' => 'required|exists:unit_of_measurements,id',
            'inventoryDataToSave.cost' => 'required|numeric|gt:0',
            'inventoryDataToSave.cost_unit_id' => 'required|exists:unit_of_measurements,id',
            'inventoryDataToSave.par_level' => 'required|numeric|gt:0',
            'inventoryDataToSave.inventory_category_id' => 'required|exists:inventory_categories,id',
            'inventoryDataToSave.inventory_unit_id' => 'required|exists:unit_of_measurements,id',
        ]);

        DB::beginTransaction();
        try{
            $uploadedImages = [];
            // foreach($validated['inventories'] as $item){
            //     $item['image'] = $item['image']->store('inventory_images', 'public');
            //     $uploadedImages[] = $item['image'];
            //     // opening quantity
            //     // foreach($this->units as $unit){
            //     //     if($unit->id == $item['opening_quantity_unit_id']){

            //     //     }
            //     // }
            //     Inventory::create([
            //         'name' => $item['name'],
            //         'code' => $item['code'],
            //         'image' => $item['image'],
            //         'opening_quantity' => $item['opening_quantity'],
            //         'quantity_on_hand' => $item['opening_quantity'],
            //         'cost' => $item['cost'],
            //         'cost_unit_id' => $item['cost_unit_id'],
            //         'par_level' => $item['par_level'],
            //         'inventory_category_id' => $item['inventory_category_id'],
            //         'inventory_unit_id' => $item['unit_of_measurement_id'],
            //     ]);
            // }

            $validated['inventoryDataToSave']['image'] = $validated['inventoryDataToSave']['image']->store('inventory_images', 'public');

            if($this->unitCategory === 'weight'){
                $inventory_unit = $this->storedUnits->find($validated['inventoryDataToSave']['inventory_unit_id']);
                $opening_quantity_unit = $this->storedUnits->find($validated['inventoryDataToSave']['opening_quantity_unit_id']);

                $opening_quantity = new Mass($validated['inventoryDataToSave']['opening_quantity'], $opening_quantity_unit->symbol);
                $final_opening_quantity = $opening_quantity->toUnit($inventory_unit->symbol);
            }
            elseif($this->unitCategory === 'volume'){
                $inventory_unit = $this->storedUnits->find($validated['inventoryDataToSave']['inventory_unit_id']);
                $opening_quantity_unit = $this->storedUnits->find($validated['inventoryDataToSave']['opening_quantity_unit_id']);

                $opening_quantity = new Volume($validated['inventoryDataToSave']['opening_quantity'], $opening_quantity_unit->symbol);
                $final_opening_quantity = $opening_quantity->toUnit($inventory_unit->symbol);
            }
            elseif($this->unitCategory === 'count'){
                $final_opening_quantity = $validated['inventoryDataToSave']['opening_quantity'];
            }

            Inventory::create([
                'name' => $validated['inventoryDataToSave']['name'],
                'code' => $validated['inventoryDataToSave']['code'],
                'image' => $validated['inventoryDataToSave']['image'],
                'opening_quantity' => $final_opening_quantity,
                'quantity_on_hand' => $final_opening_quantity,
                'unit_cost' => $validated['inventoryDataToSave']['cost'],
                'cost_unit_id' => $validated['inventoryDataToSave']['cost_unit_id'],
                'par_level' => $validated['inventoryDataToSave']['par_level'],
                'inventory_category_id' => $validated['inventoryDataToSave']['inventory_category_id'],
                'inventory_unit_id' => $validated['inventoryDataToSave']['inventory_unit_id'],
            ]);
            DB::commit();
            $this->reset('inventoryDataToSave');
        }catch(Exception $e){
            dd($e);
            Storage::disk('public')->delete($validated['inventoryDataToSave']['image']);
            DB::rollBack();
        }
    }
    public function render()
    {
        $filteredUnits = UnitOfMeasurement::where('category', $this->unitCategory)->get() ?? UnitOfMeasurement::all();
        $units = UnitOfMeasurement::all()->groupBy('category');
        $categories = InventoryCategory::all();
        
        return view('livewire.inventory.create')
            ->with(compact('units', 'filteredUnits', 'categories'));
    }
}
