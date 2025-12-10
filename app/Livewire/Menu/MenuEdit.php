<?php

namespace App\Livewire\Menu;

use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\MenuItem;
use App\Models\MenuItemCustomization;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\MenuItemCategory;
use App\Models\InventoryCategory;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use function Symfony\Component\Clock\now;

#[Layout('layouts.app')]
class MenuEdit extends Component
{
    use WithFileUploads;
    public $menu_item;
    public $name;
    public $description;
    public $image;
    public $oldImage;
    public $price;
    public $menu_item_category_id;
    public $ingredients = [];
    public $alternativeIngredients = [];
    public $alternativeUID;
    public $removableIngredients = [];
    public $additionalIngredients = [];
    public $ingredientType;
    public $showInventoryModal = false;
    public $category;
    public function mount(MenuItem $menu){
        $ingredients = Ingredient::with(['unitOfMeasurement'])->where('menu_item_id', $menu->id)->get();
        $customizations = MenuItemCustomization::with(['unitOfMeasurement'])->where('menu_item_id', $menu->id)->get();
        
        $this->menu_item = $menu;
        $this->name = $menu->name;
        $this->description = $menu->description;
        $this->oldImage = $menu->image;
        $this->price = $menu->price;
        $this->menu_item_category_id = $menu->menu_item_category_id;

        foreach($ingredients as $ingredient){
            $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($ingredient->inventory_id);  
            $this->ingredients[] = [
                'uid' => $ingredient->id, //(string) Str::uuid()
                'inventory_id' => $inventory->id,
                'image' => $inventory->image,
                'name' => $inventory->name,
                'code' => $inventory->code,
                'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                'unit_category' => $inventory->unitOfMeasurement->category,
                'quantity' => $ingredient->quantity,
            ];
        }

        foreach($customizations as $custom){
            if($custom->action == 'replace'){
                $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($custom->inventory_id); 
                $this->alternativeIngredients[] = [
                    'uid' => $custom->id, // (string) Str::uuid()
                    'ingredient_uid' => $custom->ingredient_id,
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                    'unit_category' => $inventory->unitOfMeasurement->category,
                    'quantity' => $custom->quantity,
                ];
            }
            elseif($custom->action == 'add'){
                $inventory = Inventory::with(['unitOfMeasurement'])->findOrFail($custom->inventory_id); 
                $this->additionalIngredients[] =
                [
                    'uid' => $custom->id, // (string) Str::uuid()
                    'inventory_id' => $inventory->id,
                    'image' => $inventory->image,
                    'name' => $inventory->name,
                    'code' => $inventory->code,
                    'unit_of_measurement_id' => $inventory->unit_of_measurement_id,
                    'unit_category' => $inventory->unitOfMeasurement->category,
                    'quantity' => $custom->quantity,
                ];
            }
            elseif($custom->action == 'remove'){
                $this->removableIngredients[] = $custom->ingredient_id;
            }
        }

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
                ];
        }
    }
    public function removeIngredient($type, $uid, $alternative_uid = NULL){
        if($type == 'base'){
            if(!$alternative_uid){
                $this->ingredients = array_values(array_filter($this->ingredients, fn($item) => (string) $item['uid'] !== $uid));
                $this->alternativeIngredients = array_values(array_filter($this->alternativeIngredients, fn($item) => (string) $item['ingredient_uid'] !== $uid));
                $this->removableIngredients = array_values(array_filter($this->removableIngredients, fn($item) => (string) $item !== $uid));
            }
            else{
                $this->alternativeIngredients = array_values(array_filter($this->alternativeIngredients, fn($item) => (string) $item['uid'] !== $alternative_uid));
            }
        }
        elseif($type == 'additional'){
            $this->additionalIngredients = array_values(array_filter($this->additionalIngredients, fn($item) => (string) $item['uid'] !== $uid));
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
    public function update(){
        // edit create component to remove toggled removable when removing the ingredient

        $validated = $this->validate([
            'image' => 'nullable|image',
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric|gte:0',
            'menu_item_category_id'=> 'required|exists:menu_item_categories,id',
            'ingredients.*.inventory_id' => 'required|exists:inventories,id',
            'ingredients.*.quantity' => 'required|numeric|gt:0',
            'ingredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
            'alternativeIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'alternativeIngredients.*.quantity' => 'required',
            'alternativeIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
            'additionalIngredients.*.inventory_id' => 'required|exists:inventories,id',
            'additionalIngredients.*.quantity' => 'required',
            'additionalIngredients.*.unit_of_measurement_id' => 'required|exists:unit_of_measurements,id',
        ]);

        $uploadedImages = [];

        DB::beginTransaction();
        try{
            // logic for updating the storage for image
            // add logic to delete data that is removed in the form

            // logic for updating:
            // get all data from database
            // separate the ones to be created and updated
            // update or create

            $existingIngredients = Ingredient::where('menu_item_id', $this->menu_item->id)
                ->get()
                ->keyBy('id');
            $existingCustomizations = MenuItemCustomization::where('menu_item_id', $this->menu_item->id)
                ->get()
                ->keyBy('id');

            $existingRemovableIngredients = [];

            $ingredientsCreated = [];
            $ingredientsToUpdate = [];
            $customizationsToUpdate = [];
            $customizationsToCreate = [];
            $ingredientIdForRemovable = NULL;

            // UPDATE MENU
            $menu_update_data = [
                'name' => $this->name,
                'description' => $this->description,
                'price' => $this->price,
                'menu_item_category_id' => $this->menu_item_category_id,
            ];

            if ($this->image) {
                if($this->menu_item->image && Storage::disk("public")->exists($this->menu_item->image)){
                    Storage::disk("public")->delete($this->menu_item->image);
                }
                $menu_update_data["image"] = $this->image->store("menu_images","public");
                $uploadedImages[] = $menu_update_data['image'];
            }
            else{
                unset($menu_update_data['image']);
            }

            $this->menu_item->update($menu_update_data);

            // FILTERING DATA FOR CREATING OR UPDATING
            foreach($this->ingredients as $ingredient){
                // SORTING INGREDIENT DATA FOR UPDATING AND CREATING NEW DATA
                // IF DATA EXISTS IN DATABASE
                if($existingIngredients->has($ingredient['uid'])){
                    // DATA TO UPDATE INGREDIENTS
                    $ingredientsToUpdate[] = [
                        'id' => $existingIngredients[$ingredient['uid']]->id,
                        'menu_item_id' => $this->menu_item->id,
                        'inventory_id' => $ingredient['inventory_id'],
                        'quantity' => $ingredient['quantity'],
                        'unit_of_measurement_id' => $ingredient['unit_of_measurement_id']
                    ];

                    $ingredientIdForRemovable = $existingIngredients[$ingredient['uid']]->id;

                    // SORTING ALTERNATIVE INGREDIENTS DATA
                    foreach($this->alternativeIngredients as $alternativeIngredient){
                        if($ingredient['uid'] == $alternativeIngredient['ingredient_uid']){
                            // DATA TO UPDATE CUSTOMIZATIONS
                            if($existingCustomizations->has($alternativeIngredient['uid'])){
                                $customizationsToUpdate[] = [
                                    'id' => $existingCustomizations[$alternativeIngredient['uid']]->id,
                                    'menu_item_id' => $this->menu_item->id,
                                    'ingredient_id' => $existingIngredients[$ingredient['uid']]->id, 
                                    'inventory_id' => $alternativeIngredient['inventory_id'],
                                    'quantity' => $alternativeIngredient['quantity'],
                                    'unit_of_measurement_id' => $alternativeIngredient['unit_of_measurement_id'],
                                    'action' => 'replace',
                                ];
                            }
                            // DATA TO CREATE CUSTOMIZATIONS
                            else{
                                $customizationsToCreate[] = [
                                    'menu_item_id' => $this->menu_item->id,
                                    'ingredient_id' => $existingIngredients[$ingredient['uid']]->id, 
                                    'inventory_id' => $alternativeIngredient['inventory_id'],
                                    'quantity' => $alternativeIngredient['quantity'],
                                    'unit_of_measurement_id' => $alternativeIngredient['unit_of_measurement_id'],
                                    'action' => 'replace',
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                    }
                }
                // IF NEW DATA
                else{
                    // CREATE NEW INGREDIENT
                    $createdIngredient = Ingredient::create([
                        'menu_item_id' => $this->menu_item->id,
                        'inventory_id' => $ingredient['inventory_id'],
                        'quantity' => $ingredient['quantity'],
                        'unit_of_measurement_id' => $ingredient['unit_of_measurement_id'],
                    ]);

                    // FOR IDENTIFYING NEWLY CREATED DATA
                    // FOR DELETING REMOVED DATA
                    $ingredientsCreated[] = $createdIngredient->id;
                    $ingredientIdForRemovable = $createdIngredient->id;

                    // DATA TO CREATE CUSTOMIZATIONS
                    foreach($this->alternativeIngredients as $alternativeIngredient){
                        if($ingredient['uid'] == $alternativeIngredient['ingredient_uid']){
                            $customizationsToCreate[]= [
                                'menu_item_id' => $this->menu_item->id,
                                'ingredient_id' => $createdIngredient->id, 
                                'inventory_id' => $alternativeIngredient['inventory_id'],
                                'quantity' => $alternativeIngredient['quantity'],
                                'unit_of_measurement_id' => $alternativeIngredient['unit_of_measurement_id'],
                                'action' => 'replace',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                    }

                }

                // SORTING REMOVABLE INGREDIENTS FOR CUSTOMIZATIONS
                if(in_array($ingredient['uid'], $this->removableIngredients)){
                    // SEARCH IF IT EXISTS IN DATABASE
                    $search = MenuItemCustomization::where('ingredient_id', $ingredient['uid'])
                        ->where('action', 'remove')
                        ->first();
                        
                    // IF NO SORT DATA FOR CREATING CUSTOMIZATIONS
                    if(!$search){
                        $customizationsToCreate[] = [
                            'menu_item_id' => $this->menu_item->id,
                            'ingredient_id' => $ingredientIdForRemovable,
                            'action' => 'remove',
                            'created_at' => now(),
                            'updated_at' => now(), 
                        ];
                    }
                    // IF YES SORT FOR IDENTIFYING REMOVED CUSTOMIZATIONS
                    else{
                        $existingRemovableIngredients[] = $search->id;
                    }
                }
            }

            // SORING ADDITIONAL INGREDIENTS FOR CUSTOMIZATIONS
            foreach($this->additionalIngredients as $additionalIngredient){
                // IF IT EXISTS IN DATABASE
                if($existingCustomizations->has($additionalIngredient['uid'])){
                    $customizationsToUpdate[] = [
                        'id' => $existingCustomizations[$additionalIngredient['uid']]->id,
                        'menu_item_id' => $this->menu_item->id,
                        'ingredient_id' => NULL,
                        'inventory_id' => $additionalIngredient['inventory_id'],
                        'quantity' => $additionalIngredient['quantity'],
                        'unit_of_measurement_id' => $additionalIngredient['unit_of_measurement_id'],
                        'action' => 'add'
                    ];
                }
                // IF IT DOESN'T EXIST IN DATABASE
                else{
                    $customizationsToCreate[] = [
                        'menu_item_id' => $this->menu_item->id,
                        'inventory_id' => $additionalIngredient['inventory_id'],
                        'quantity' => $additionalIngredient['quantity'],
                        'unit_of_measurement_id' => $additionalIngredient['unit_of_measurement_id'],
                        'action' => 'add',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // IDENTIFYING INGREDIENT DATA TO BE REMOVED
            // RETREIVING EXISTING INGREDIENT IDS
            $oldIngredientsID = $existingIngredients->keys()->toArray();
            // RETREIVING ADDED AND UPDATED INGREDIENT IDS
            $newIngredientsID = array_merge($ingredientsCreated, collect($ingredientsToUpdate)->pluck('id')->toArray());
            // IDENTIFYING WHICH DATA IS TO BE REMOVED
            $ingredientsToRemove = array_diff($oldIngredientsID, $newIngredientsID);

            // IDENTIFYING CUSTOMIZATION DATA TO BE REMOVED
            // RETREIVING EXISITNG CUSTOMIZATION IDS
            $oldCustomizationsID = $existingCustomizations->keys()->toArray();
            // RETREIVING CUSTOMIZATIONS TO BE UPDATED AND THE EXISTING IDS OF THE REMOVABLE 
            $newCustomizationsID = array_merge(collect($customizationsToUpdate)->pluck('id')->toArray(), $existingRemovableIngredients);
            // IDENTIFYING WHICH DATA IS TO BE REMOVED
            $customizationsToRemove = array_diff($oldCustomizationsID, $newCustomizationsID);


            // dd(
            //     // $oldIngredientsID,      
            //     // $newIngredientsID,            
            //     $ingredientsToRemove,           
            //     // $oldCustomizationsID,
            //     // $newCustomizationsID,
            //     $customizationsToRemove,
            //     $ingredientsToUpdate,
            //     $customizationsToCreate,
            //     $customizationsToUpdate
            // );

            Ingredient::upsert($ingredientsToUpdate, ['id'], ['quantity', 'unit_of_measurement_id']);
            MenuItemCustomization::insert($customizationsToCreate);
            MenuItemCustomization::upsert($customizationsToUpdate, ['id'], ['quantity', 'unit_of_measurement_id']);
            Ingredient::destroy($ingredientsToRemove);
            MenuItemCustomization::destroy($customizationsToRemove);

            DB::commit();
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

        return view('livewire.menu.menu-edit')
            ->with(compact('menuItemCategories', 'categories', 'units', 'active', 'inventories'));
    }
}
