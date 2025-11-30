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

#[Layout('layouts.app')]
class InventoryEdit extends Component
{
    public $inventory;
    public $name;
    public $code;
    public $storedImage;
    public $image;
    public $quantity;
    public $price;
    public $par_level;
    public $inventory_category_id;
    public $unit_of_measurement_id;
    use WithFileUploads;
    public function mount(Inventory $inventory){
        $this->inventory = $inventory;
        $this->name = $inventory->name;
        $this->code = $inventory->code;
        $this->storedImage = $inventory->image;
        $this->quantity = $inventory->quantity;
        $this->price = $inventory->price;
        $this->par_level = $inventory->par_level;
        $this->inventory_category_id = $inventory->inventory_category_id;
        $this->unit_of_measurement_id = $inventory->unit_of_measurement_id;
    }
    public function update(){
        $validated = $this->validate([
            'name' => 'required',
            'code' => 'required',
            'image' => 'nullable',
            'quantity' => 'required',
            'price' => 'required',
            'par_level' => 'required',
            'inventory_category_id' => 'required',
            'unit_of_measurement_id' => 'required',
        ]);

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

            $this->inventory->update($validated);
            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    }
    public function render()
    {
        $categories = InventoryCategory::all();
        $units = UnitOfMeasurement::all()->groupBy('category');
        
        return view('livewire.inventory.inventory-edit')
            ->with(compact('categories', 'units'));
    }
}
