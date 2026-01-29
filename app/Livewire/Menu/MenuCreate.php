<?php

namespace App\Livewire\Menu;

use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\InventoryCategory;
use App\Models\MenuItem;
use App\Models\MenuItemCategory;
use App\Models\MenuItemCustomization;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use PDO;

#[Layout('layouts.app')]
class MenuCreate extends Component
{
    use WithFileUploads;
    public $inventory;
    public $uom;
    public $name;
    public $description;
    public $image;
    public $cost = 0;
    public $price;
    public $is_vat_exempt = false;
    public $menu_item_category_id;
    public $ingredients = [];
    public $alternativeIngredients = [];
    public $alternativeUID;
    public $removableIngredients = [];
    public $additionalIngredients = [];
    public $ingredientType;
    public $showInventoryModal = false;
    public $category;
    public function mount(){
        $this->inventory = Inventory::with(['category', 'inventoryUnit', 'costUnit'])->get();
        $this->uom = UnitOfMeasurement::all();
    }
    public function openInventoryModal($type, $alternative_uid = NULL){
        if($alternative_uid){
            $this->alternativeUID = $alternative_uid;
        }
        $this->showInventoryModal = true;
        $this->ingredientType = $type;
    }
    public function closeInventoryModal(){
        $this->showInventoryModal = false;
        $this->reset('alternativeUID', 'ingredientType');
    }
    public function switchTabs($id){
        if($id !== ''){
            $this->category = $id;
        }
        else{
            $this->category = '';
        }
    }
    public function addIngredient($id){
        if($this->ingredientType == 'base'){
            if(!$this->alternativeUID){
                // $inventory = Inventory::with(['inventoryUnit', 'costUnit'])->findOrFail($id);
                $inventory = $this->inventory->find($id);
                $this->ingredients[] =
                    [
                        'uid' => (string) Str::uuid(),
                        'inventory_id' => $inventory->id,
                        'image' => $inventory->image,
                        'name' => $inventory->name, 
                        'code' => $inventory->code,
                        'cost' => 0,
                        'unit_of_measurement_id' => $inventory->inventory_unit_id,
                        'unit_category' => $inventory->inventoryUnit->category,
                        'quantity_used' => 0,
                    ];
            }
            else{
                // $inventory = Inventory::with(['inventoryUnit', 'costUnit'])->findOrFail($id);
                $inventory = $this->inventory->find($id);
                $this->alternativeIngredients[] = 
                [
                    'uid' => (string) Str::uuid(),
                    'ingredient_uid' => $this->alternativeUID,
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_category' => $inventory->inventoryUnit->category,
                    'quantity_used' => 0,
                    'price' => 0,
                    'cost' => 0,
                    // 'is_vat_exempt' => false,
                    'unit_of_measurement_id' => $inventory->inventory_unit_id,
                ];
            }
        }
        elseif($this->ingredientType == 'additional'){
            // $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($id);
            $inventory = $this->inventory->find($id);
            $this->additionalIngredients[] =
                [
                    'uid' => (string) Str::uuid(),
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_category' => $inventory->inventoryUnit->category,
                    'quantity_used' => 0,
                    'price' => 0,
                    'cost' => 0,
                    // 'is_vat_exempt' => false,
                    'unit_of_measurement_id' => $inventory->inventory_unit_id,
                ];
        }
    }
    public function removeIngredient($type, $uid, $alternative_uid = NULL){
        if($type == 'base'){
            if(!$alternative_uid){
                $index = array_search(
                    $uid,
                    array_column($this->ingredients, 'uid')
                );
                $this->cost -= $this->ingredients[$index]['cost'];
                $this->ingredients = array_values(array_filter($this->ingredients, fn($item) => $item['uid'] !== $uid));
                $this->alternativeIngredients = array_values(array_filter($this->alternativeIngredients, fn($item) => $item['ingredient_uid'] !== $uid));
            }
            else{
                $this->alternativeIngredients = array_values(array_filter($this->alternativeIngredients, fn($item) => $item['uid'] !== $alternative_uid));
            }
        }
        elseif($type == 'additional'){
            $this->additionalIngredients = array_values(array_filter($this->additionalIngredients, fn($item) => $item['uid'] !== $uid));
        }
    }
    public function computeCost($type, $index, $value){
        if($value > 0){
            if($type === 'ingredients'){
                $this->cost -= $this->ingredients[$index]['cost'];
                $inventory = $this->inventory->find($this->ingredients[$index]['inventory_id']);
                $unit = $this->uom->find( $this->ingredients[$index]['unit_of_measurement_id']);
                $category = $unit->category;
                $quantity = $this->ingredients[$index]['quantity_used'];
    
                $computed = $inventory->computeCostPerUnit($unit->symbol, $category);
    
                $cost = round($quantity * $computed, 2);
    
                $this->ingredients[$index]['cost'] = $cost;
    
                $this->cost += $cost;
    
                // dd($inventory->costUnit->symbol, $unit->symbol, $computed, $cost);
            }
            elseif($type === 'alternative ingredients'){
                $inventory = $this->inventory->find($this->alternativeIngredients[$index]['inventory_id']);
                $unit = $this->uom->find( $this->alternativeIngredients[$index]['unit_of_measurement_id']);
                $category = $unit->category;
                $quantity = $this->alternativeIngredients[$index]['quantity_used'];
    
                $computed = $inventory->computeCostPerUnit($unit->symbol, $category);
    
                $cost = round($quantity * $computed, 2);
    
                $this->alternativeIngredients[$index]['cost'] = $cost;
    
                // dd($inventory->costUnit->symbol, $unit->symbol, $computed, $cost);
            }
            elseif($type === 'additional ingredients'){
                $inventory = $this->inventory->find($this->additionalIngredients[$index]['inventory_id']);
                $unit = $this->uom->find( $this->additionalIngredients[$index]['unit_of_measurement_id']);
                $category = $unit->category;
                $quantity = $this->additionalIngredients[$index]['quantity_used'];
    
                $computed = $inventory->computeCostPerUnit($unit->symbol, $category);
    
                $cost = round($quantity * $computed, 2);
    
                $this->additionalIngredients[$index]['cost'] = $cost;
    
                // dd($inventory->costUnit->symbol, $unit->symbol, $computed, $cost);
            }
        }
        else{
            $this->ingredients[$index]['quantity_used'] = 0;
            $this->ingredients[$index]['cost'] = 0;
        }
    }
    public function updatedIngredients($value, $key){
        [$index, $field] = explode('.', $key);
        // dd($key, $index, $field, $value);

        if($field === 'quantity_used'){
            $this->computeCost('ingredients', $index, $value);
        }

        if($field === 'unit_of_measurement_id'){
            $this->computeCost('ingredients', $index, $value);
        }
    }
    public function updatedAlternativeIngredients($value, $key){
        [$index, $field] = explode('.', $key);
        // dd($key, $index, $field, $value);

        if($field === 'quantity_used' && $value > 0){
            $this->computeCost('alternative ingredients', $index, $value);
        }

        if($field === 'unit_of_measurement_id'){
            $this->computeCost('alternative ingredients', $index, $value);
        }
    }
    public function updatedAdditionalIngredients($value, $key){
        [$index, $field] = explode('.', $key);
        // dd($key, $index, $field, $value);

        if($field === 'quantity_used' && $value > 0){
            $this->computeCost('additional ingredients', $index, $value);
        }

        if($field === 'unit_of_measurement_id'){
            $this->computeCost('additional ingredients', $index, $value);
        }
    }
    public function save(){
        $validated = $this->validate([
            // menu item 
            'image' => 'required|image',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|gte:0',
            'cost' => 'required|numeric|gte:0',
            'is_vat_exempt' => 'required|',
            'menu_item_category_id'=> 'required|exists:menu_item_categories,id',

            // ingredient
            'ingredients.*.inventory_id' => 'required|exists:inventories,id',
            'ingredients.*.quantity_used' => 'required|numeric|gt:0',
            'ingredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',

            // CUSTOMIZATIONS

            // alternative
            'alternativeIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'alternativeIngredients.*.quantity_used' => 'required',
            'alternativeIngredients.*.price' => 'required|numeric|gte:0',
            'alternativeIngredients.*.cost' => 'required|numeric|gte:0',
            'alternativeIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',

            // additional
            'additionalIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'additionalIngredients.*.quantity_used' => 'required',
            'additionalIngredients.*.price' => 'required|numeric|gte:0',
            'additionalIngredients.*.cost' => 'required|numeric|gte:0',
            'additionalIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
        ]);

        $uploadedImages = [];

        DB::beginTransaction();
        try{

            $uploadedImage = $this->image->store('menu_images', 'public');
            $uploadedImages[] = $uploadedImage;

            // INSERT MENU ITEM
            $menu_item = MenuItem::create([
                'name' => $this->name,
                'description' => $this->description,
                'image' => $uploadedImage,
                'price' => $this->price,
                'cost' => $this->cost,
                'is_vat_exempt' => $this->is_vat_exempt,
                'menu_item_category_id' => $this->menu_item_category_id
            ]);

            foreach($this->ingredients as $ingredient){
                // INSERT INGREDIENT
                $createdIngredient = Ingredient::create([
                    'menu_item_id' => $menu_item->id,
                    'inventory_id' => $ingredient['inventory_id'],
                    'quantity_used' => $ingredient['quantity_used'],
                    'unit_of_measurement_id' => $ingredient['unit_of_measurement_id'],
                ]);
                // INSERT ALTERNATIVE INGREDIENT
                foreach($this->alternativeIngredients as $alternativeIngredient){
                    if($ingredient['uid'] == $alternativeIngredient['ingredient_uid']){
                        MenuItemCustomization::create([
                            'menu_item_id' => $menu_item->id,
                            'ingredient_id' => $createdIngredient->id, 
                            'inventory_id' => $alternativeIngredient['inventory_id'],
                            'quantity_used' => $alternativeIngredient['quantity_used'],
                            'price' => $alternativeIngredient['price'],
                            'cost' => $alternativeIngredient['cost'],
                            'unit_of_measurement_id' => $alternativeIngredient['unit_of_measurement_id'],
                            'action' => 'replace'
                        ]);
                    }
                }
                if(in_array($ingredient['uid'], $this->removableIngredients)){
                    MenuItemCustomization::create([
                        'menu_item_id' => $menu_item->id,
                        'ingredient_id' => $createdIngredient->id,
                        'price' => 0,
                        'cost' => 0,
                        'action' => 'remove' 
                    ]);
                }
            }
            // INSERT ADDITIONAL INGREDIENTS
            foreach($this->additionalIngredients as $additionalIngredient){
                MenuItemCustomization::create([
                    'menu_item_id' => $menu_item->id,
                    'inventory_id' => $additionalIngredient['inventory_id'],
                    'quantity_used' => $additionalIngredient['quantity_used'],
                    'price' => $additionalIngredient['price'],
                    'cost' => $additionalIngredient['cost'],
                    'unit_of_measurement_id' => $additionalIngredient['unit_of_measurement_id'],
                    'action' => 'add'
                ]);
            }
            DB::commit();
            $this->reset('name', 'description', 'image', 'price', 'menu_item_category_id', 'ingredients', 'alternativeIngredients', 'removableIngredients', 'additionalIngredients');
        }catch(Exception $e){
            DB::rollBack();
            foreach($uploadedImages as $image){
                Storage::disk('public')->delete($image);
            }
            dd($e);
        }
    }
    public function render()
    {
        $menuItemCategories = MenuItemCategory::all();
        $categories = InventoryCategory::all();
        $units = UnitOfMeasurement::all()->groupBy('category');
        $active = $this->category;
        $inventories = $this->category ? 
            Inventory::with(['category','inventoryUnit', 'costUnit'])
                ->where('inventory_category_id', $this->category)
                ->paginate(10)
            :
            Inventory::with(['category','inventoryUnit', 'costUnit'])
                ->paginate(10);

        return view('livewire.menu.menu-create')
            ->with(compact('menuItemCategories', 'active', 'categories', 'inventories', 'units'));
    }
}

// public function incrementQuantity($index, $type){
//     if($type === 'ingredient'){
//         $this->ingredients[$index]['quantity_used']++;
//     }
//     elseif($type === 'alternative'){
//         $this->alternativeIngredients[$index]['quantity']++;
//     }
//     elseif($type === 'additional'){
//         $this->additionalIngredients[$index]['quantity']++;
//     }
// }
// public function decrementQuantity($index, $type){
//     if($type === 'ingredient'){
//         if(!$this->ingredients[$index]['quantity_used'] < 1){
//             $this->ingredients[$index]['quantity_used']--;
//         }
//     }
//     elseif($type === 'alternative'){
//         if(!$this->alternativeIngredients[$index]['quantity'] < 1){
//             $this->alternativeIngredients[$index]['quantity']--;
//         }
//     }
//     elseif($type === 'additional'){
//         if(!$this->additionalIngredients[$index]['quantity'] < 1){
//             $this->additionalIngredients[$index]['quantity']--;
//         }
//     }
// }
