<?php

namespace App\Livewire\Inventory;

use App\Models\Inventory;
use App\Models\InventoryCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InventoryList extends Component
{
    use WithPagination;
    public $category;
    public $showDeleteModal = false;
    public $deleteId;

    public function switchTabs($id){
        if($id !== ''){
            $this->category = $id;
        }
        else{
            $this->category = '';
        }
    }
    
    public function openDeleteModal($id){
        $this->showDeleteModal = true;
        $this->deleteId = $id;
    }
    public function closeDeleteModal(){
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }
    public function deleteInventory(){
        DB::beginTransaction();
        try{
            $inventory = Inventory::findOrFail($this->deleteId);
            $inventory->delete();
            DB::commit();
            $this->showDeleteModal = false;
        }catch(Exception $e){
            dd($e);
            DB::rollBack();
        }
    }
    public function render()
    {
        $active = $this->category;
        $categories = InventoryCategory::all();
        $inventories = $this->category ? 
            Inventory::with(['category','inventoryUnit', 'costUnit'])
                ->where('inventory_category_id', $this->category)
                ->paginate(10)
            :
            Inventory::with(['category','inventoryUnit', 'costUnit'])
                ->paginate(10);

        return view('livewire.inventory.inventory-list')
            ->with(compact('inventories', 'categories', 'active'));
    }
}
