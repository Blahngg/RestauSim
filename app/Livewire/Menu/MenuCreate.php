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
    public $code;
    public $name;
    public $description;
    public $image;
    public $base_price;
    public $vat_rate = 0.12;
    public $is_vat_inclusive = false;
    public $menu_item_category_id;
    public $ingredients = [];
    public $alternativeIngredients = [];
    public $alternativeUID;
    public $removableIngredients = [];
    public $additionalIngredients = [];
    public $ingredientType;
    public $showInventoryModal = false;
    public $category;
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
                $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($id);
                $this->ingredients[] =
                    [
                        'uid' => (string) Str::uuid(),
                        'inventory_id' => $inventory->id,
                        'image' => $inventory->image,
                        'name' => $inventory->name,
                        'code' => $inventory->code,
                        'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                        'unit_category' => $inventory->unitOfMeasurement->category,
                        'quantity' => 0,
                    ];
            }
            else{
                $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($id);
                $this->alternativeIngredients[] = 
                [
                    'uid' => (string) Str::uuid(),
                    'ingredient_uid' => $this->alternativeUID,
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                    'unit_category' => $inventory->unitOfMeasurement->category,
                    'quantity' => 0,
                    'price' => null,
                ];
            }
        }
        elseif($this->ingredientType == 'additional'){
            $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($id);
            $this->additionalIngredients[] =
                [
                    'uid' => (string) Str::uuid(),
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                    'unit_category' => $inventory->unitOfMeasurement->category,
                    'quantity' => 0,
                    'price' => null,
                ];
        }
    }
    public function removeIngredient($type, $uid, $alternative_uid = NULL){
        if($type == 'base'){
            if(!$alternative_uid){
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
    public function incrementQuantity($index, $type){
        if($type === 'ingredient'){
            $this->ingredients[$index]['quantity']++;
        }
        elseif($type === 'alternative'){
            $this->alternativeIngredients[$index]['quantity']++;
        }
        elseif($type === 'additional'){
            $this->additionalIngredients[$index]['quantity']++;
        }
    }
    public function decrementQuantity($index, $type){
        if($type === 'ingredient'){
            if(!$this->ingredients[$index]['quantity'] < 1){
                $this->ingredients[$index]['quantity']--;
            }
        }
        elseif($type === 'alternative'){
            if(!$this->alternativeIngredients[$index]['quantity'] < 1){
                $this->alternativeIngredients[$index]['quantity']--;
            }
        }
        elseif($type === 'additional'){
            if(!$this->additionalIngredients[$index]['quantity'] < 1){
                $this->additionalIngredients[$index]['quantity']--;
            }
        }
    }
    public function save(){
        $validated = $this->validate([
            'image' => 'required|image',
            'code' => 'required|unique:menu_items,code',
            'name' => 'required',
            'description' => 'nullable',
            'base_price' => 'required|numeric|gte:0',
            'vat_rate' => 'decimal:2',
            'is_vat_inclusive' => 'boolean',
            'menu_item_category_id'=> 'required|exists:menu_item_categories,id',
            'ingredients.*.inventory_id' => 'required|exists:inventories,id',
            'ingredients.*.quantity' => 'required|numeric|gt:0',
            'ingredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
            'alternativeIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'alternativeIngredients.*.quantity' => 'required',
            'alternativeIngredients.*.price' => 'nullable|integer',
            'alternativeIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
            'additionalIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'additionalIngredients.*.quantity' => 'required',
            'additionalIngredients.*.price' => 'nullable|integer',
            'additionalIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
        ]);

        $uploadedImages = [];

        DB::beginTransaction();
        // Doesn't have lines to store images to local storage
        try{

            $uploadedImage = $this->image->store('menu_images', 'public');
            $uploadedImages[] = $uploadedImage;

            // INSERT MENU ITEM
            $menu_item = MenuItem::create([
                'code' => $this->code,
                'name' => $this->name,
                'description' => $this->description,
                'image' => $uploadedImage,
                'base_price' => $this->base_price,
                'menu_item_category_id' => $this->menu_item_category_id,
                'vat_rate' => $this->vat_rate,
                'is_vat_inclusive' => $this->is_vat_inclusive,
            ]);

            foreach($this->ingredients as $ingredient){
                // INSERT INGREDIENT
                $createdIngredient = Ingredient::create([
                    'menu_item_id' => $menu_item->id,
                    'inventory_id' => $ingredient['inventory_id'],
                    'quantity' => $ingredient['quantity'],
                    'unit_of_measurement_id' => $ingredient['unit_of_measurement_id'],
                ]);
                // INSERT ALTERNATIVE INGREDIENT
                foreach($this->alternativeIngredients as $alternativeIngredient){
                    if($ingredient['uid'] == $alternativeIngredient['ingredient_uid']){
                        MenuItemCustomization::create([
                            'menu_item_id' => $menu_item->id,
                            'ingredient_id' => $createdIngredient->id, 
                            'inventory_id' => $alternativeIngredient['inventory_id'],
                            'quantity' => $alternativeIngredient['quantity'],
                            'unit_of_measurement_id' => $alternativeIngredient['unit_of_measurement_id'],
                            'action' => 'replace',
                            'price' => $alternativeIngredient['price'],
                        ]);
                    }
                }
                if(in_array($ingredient['uid'], $this->removableIngredients)){
                    MenuItemCustomization::create([
                        'menu_item_id' => $menu_item->id,
                        'ingredient_id' => $createdIngredient->id,
                        'action' => 'remove' 
                    ]);
                }
            }
            // INSERT ADDITIONAL INGREDIENTS
            foreach($this->additionalIngredients as $additionalIngredient){
                MenuItemCustomization::create([
                    'menu_item_id' => $menu_item->id,
                    'inventory_id' => $additionalIngredient['inventory_id'],
                    'quantity' => $additionalIngredient['quantity'],
                    'unit_of_measurement_id' => $additionalIngredient['unit_of_measurement_id'],
                    'action' => 'add',
                    'price' => $additionalIngredient['price'],
                ]);
            }
            DB::commit();
            $this->reset('code', 'name', 'description', 'image', 'base_price', 'menu_item_category_id', 'vat_rate', 'is_vat_inclusive', 'ingredients', 'alternativeIngredients', 'removableIngredients', 'additionalIngredients');
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
            Inventory::with(['category','unitOfMeasurement'])
                ->where('inventory_category_id', $this->category)
                ->paginate(10)
            :
            Inventory::with(['category','unitOfMeasurement'])
                ->paginate(10);

        return view('livewire.menu.menu-create')
            ->with(compact('menuItemCategories', 'active', 'categories', 'inventories', 'units'));
    }
}
