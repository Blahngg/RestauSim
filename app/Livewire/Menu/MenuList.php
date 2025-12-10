<?php

namespace App\Livewire\Menu;

use App\Models\MenuItem;
use App\Models\MenuItemCategory;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
#[Layout('layouts.app')]
class MenuList extends Component
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
    public function deleteMenu(){
        DB::beginTransaction();
        try{
            $menu = MenuItem::findOrFail($this->deleteId);
            $menu->delete();
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
        $categories = MenuItemCategory::all();
        $menuItems = $this->category ? 
            MenuItem::with(['category', 'ingredients', 'customizations'])
                ->where('menu_item_category_id', $this->category)
                ->paginate(10)
            :
            MenuItem::with(['category', 'ingredients', 'customizations'])
                ->paginate(10);
        return view('livewire.menu.menu-list')
            ->with(compact('active', 'categories', 'menuItems'));
    }
}
