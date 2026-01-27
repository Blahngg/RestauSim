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
use Livewire\Volt\Compilers\Mount;
use Livewire\WithFileUploads;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

#[Layout('layouts.app')]
class InventoryEdit extends Component
{
    use WithFileUploads;
    public $storedUnits;
    public $unitCategory;
    public $inventory;
    public $name;
    public $code;
    public $storedImage;
    public $image;
    public $unit_cost;
    public $cost_unit_id;
    public $par_level;
    public $inventory_category_id;
    public $inventory_unit_id;
    public function mount(Inventory $inventory){
        $this->inventory = $inventory->load('category', 'inventoryUnit', 'costUnit');
        $this->name = $inventory->name;
        $this->code = $inventory->code;
        $this->storedImage = $inventory->image;
        $this->unit_cost = $inventory->unit_cost;
        $this->cost_unit_id = $inventory->cost_unit_id;
        $this->par_level = $inventory->par_level;
        $this->inventory_category_id = $inventory->inventory_category_id;
        $this->inventory_unit_id = $inventory->inventory_unit_id;

        $this->storedUnits = UnitOfMeasurement::all();
        $this->unitCategory = $inventory->inventoryUnit->category;
    }
    public function updatedInventoryUnitId($value){
        // dd($value);
        // dd($this->storedUnits);
        // foreach($this->storedUnits as $unit){
        //     if($unit->id === (int) $value){
        //         $this->unitCategory = $unit->category;
        //     }
        // }

        // $this->unitCategory = $this->storedUnits->find($value)?->category;

        // if($this->unitCategory !== $this->storedUnits->find($this->cost_unit_id)?->category){
        //     $this->cost_unit_id = '';
        // }

        // if inventory unit was changed, convert the par level to the new unit
        // if($this->storedUnits->find($value)->category === 'weight'){
        //     $old_par_level = new Mass($this->par_level, $this->inventory->inventoryUnit->symbol);
        //     $this->par_level = $old_par_level->toUnit($this->storedUnits->find($value)->symbol);
        // }
        // elseif($this->storedUnits->find($value)->category === 'weight'){
        //     $old_par_level = new Volume($this->par_level, $this->inventory->inventoryUnit->symbol);
        //     $this->par_level = $old_par_level->toUnit($this->storedUnits->find($value)->symbol);
        // }
        
        // fix: if inventory unit is changed continuously, the previous value will be converted not the database value

        //if inventory unit was changed, set par level to null
        $this->par_level = null;
    }
    public function update(){
        $validated = $this->validate([
            'name' => 'required|string',
            'code' => 'required|unique:inventories,code,' . $this->inventory->id,
            'image' => 'nullable',
            'unit_cost' => 'required|numeric|gt:0',
            'cost_unit_id' => 'required|exists:unit_of_measurements,id',
            'par_level' => 'required',
            'inventory_category_id' => 'required|exists:inventory_categories,id',
            'inventory_unit_id' => 'required|exists:unit_of_measurements,id',
        ]);

        // dd((int) $this->inventory_unit_id, $this->inventory->inventoryUnit->id);
        
        DB::beginTransaction();
        try{
            if ($this->image) {
                if($this->inventory->image && Storage::disk("public")->exists($this->inventory->image)){
                    Storage::disk("public")->delete($this->inventory->image);
                }
                $validated["image"] = $this->image->store("inventory_images","public");
            }
            else{
                unset($validated['image']);
            }

            // if the inventory unit was changed
            if((int) $this->inventory_unit_id !== $this->inventory->inventoryUnit->id){
                // convert quantity on hand and opening quantity to new unit
                if($this->storedUnits->find($this->inventory_unit_id)?->category === 'weight'){
                    // opening quantity
                    $oldOpeningQuantity =  new Mass($this->inventory->opening_quantity, $this->inventory->inventoryUnit->symbol);
                    $convertedOpeningQuantity = $oldOpeningQuantity->toUnit($this->storedUnits->find($this->inventory_unit_id)?->symbol);

                    // quantity on hand
                    $oldQuantityOnHand =  new Mass($this->inventory->quantity_on_hand, $this->inventory->inventoryUnit->symbol);
                    $convertedQuantityOnHand = $oldOpeningQuantity->toUnit($this->storedUnits->find($this->inventory_unit_id)?->symbol);

                    $validated['opening_quantity'] = $convertedOpeningQuantity;
                    $validated['quantity_on_hand'] = $convertedQuantityOnHand;
                }
                elseif($this->storedUnits->find($this->inventory_unit_id)?->category === 'volume'){
                    // opening quantity
                    $oldOpeningQuantity =  new Volume($this->inventory->opening_quantity, $this->inventory->inventoryUnit->symbol);
                    $convertedOpeningQuantity = $oldOpeningQuantity->toUnit($this->storedUnits->find($this->inventory_unit_id)?->symbol);

                    // quantity on hand
                    $oldQuantityOnHand =  new Volume($this->inventory->quantity_on_hand, $this->inventory->inventoryUnit->symbol);
                    $convertedQuantityOnHand = $oldOpeningQuantity->toUnit($this->storedUnits->find($this->inventory_unit_id)?->symbol);

                    $validated['opening_quantity'] = $convertedOpeningQuantity;
                    $validated['quantity_on_hand'] = $convertedQuantityOnHand;
                }
                elseif($this->storedUnits->find($this->inventory_unit_id)?->category === 'count'){

                }
            }

            // dd($validated);

            $this->inventory->update($validated);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    }
    public function render()
    {
        // $filteredUnits = UnitOfMeasurement::where('category', $this->unitCategory)->get() ?? UnitOfMeasurement::all();
        $filteredUnits = $units = UnitOfMeasurement::where('category', $this->inventory->inventoryUnit->category)->get();
        $categories = InventoryCategory::all();
        
        return view('livewire.inventory.inventory-edit')
            ->with(compact('filteredUnits', 'units', 'categories'));
    }
}
