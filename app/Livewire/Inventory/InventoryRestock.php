<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use PhpUnitsOfMeasure\PhysicalQuantity\Mass;
use PhpUnitsOfMeasure\PhysicalQuantity\Volume;

#[Layout('layouts.app')]
class InventoryRestock extends Component
{
    public $category;
    public $showSelectModal = false;
    public $selectedInventories = []; 
    public function openSelectModal(){
        $this->showSelectModal = true;
    }
    public function closeSelectModal(){
        $this->showSelectModal = false;
    }
    public function switchTabs($id){
        if($id !== ''){
            $this->category = $id;
        }
        else{
            $this->category = '';
        }
    }
    public function addInventory($id){

        $match = array_search($id, array_column($this->selectedInventories, 'id'));

        if($match === false){
            $inventoryData = Inventory::with(['unitOfMeasurement'])->findOrFail($id);
            $this->selectedInventories[] = 
            [
                'id' => $inventoryData->id,
                'image' => $inventoryData->image,
                'name' => $inventoryData->name,
                'code' => $inventoryData->code,
                'quantity' =>$inventoryData->quantity,
                'unit_of_measurement' => $inventoryData->unitOfMeasurement->symbol,
                'unit_category' => $inventoryData->unitOfMeasurement->category,
                'addQuantity' => '', 
                'addUnitOfMeasurement' => $inventoryData->unitOfMeasurement->symbol,
            ]; 
        }
        else{
            $this->selectedInventories = array_values(array_filter($this->selectedInventories, fn($item) => $item['id'] !== $id));
        }
    }
    public function restock(){
        $validated = $this->validate([
            'selectedInventories.*.id' => 'required|exists:inventories,id',
            'selectedInventories.*.addQuantity' => 'required|numeric|gt:0',
            'selectedInventories.*.unit_of_measurement' => 'required|exists:unit_of_measurements,symbol',
            'selectedInventories.*.unit_category' => 'required|exists:unit_of_measurements,category',
            'selectedInventories.*.addUnitOfMeasurement' => 'required|exists:unit_of_measurements,symbol'
        ]);

        DB::beginTransaction();
        try{
            foreach($validated['selectedInventories'] as $inventoryItem){
                $restockQuantity = null;
                if($inventoryItem['unit_category'] == 'weight'){
                    $restockQuantity = new Mass($inventoryItem['addQuantity'], $inventoryItem['addUnitOfMeasurement']);
                }
                elseif($inventoryItem['unit_category'] == 'volume'){
                    $restockQuantity = new Volume($inventoryItem['addQuantity'], $inventoryItem['addUnitOfMeasurement']);
                }
    
                Inventory::findOrFail($inventoryItem['id'])->increment('quantity', $restockQuantity->toUnit($inventoryItem['unit_of_measurement']));
            }
            $this->reset(['selectedInventories']);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }

    }
    public function render()
    {
        $active = $this->category;
        $units = UnitOfMeasurement::all()->groupBy('category');
        $categories = InventoryCategory::all();
        $selectedInventories = $this->selectedInventories;
        $inventories = $this->category ? 
            Inventory::with(['category','unitOfMeasurement'])
                ->where('inventory_category_id', $this->category)
                ->paginate(10)
            :
            Inventory::with(['category','unitOfMeasurement'])
                ->paginate(10);
        
        return view('livewire.inventory.inventory-restock')
            ->with(compact('inventories', 'categories', 'selectedInventories', 'active', 'units'));
    }
}
