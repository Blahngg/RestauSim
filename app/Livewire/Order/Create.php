<?php

namespace App\Livewire\Order;

use App\Events\OrderCancelled;
use App\Events\OrderSaved;
use App\Models\ItemOrder;
use App\Models\ItemOrderCustomization;
use App\Models\MenuItem;
use App\Models\MenuItemCategory;
use App\Models\MenuItemCustomization;
use App\Models\Order;
use App\Models\Table;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public $order;
    public $category;
    public $table;
    public $customizations;
    public $showCustomizationModal = false;
    public $menu;
    public $ordersToAdd = [];
    public $orders = [];
    public $orderCount = 0;
    public function mount(Table $table){
        $this->table = $table;
        $this->customizations = MenuItemCustomization::with(['ingredient', 'inventory', 'unitOfMeasurement'])->get();

        //Search for existing orders
        $orders = Order::with(['items'])->where('table_id', $this->table->id)->limit(2)->get();
        if($orders->count() === 1){
            $this->order = $orders->first();
            // initialize existing order items
            foreach($this->order->items as $item){
                if($item->status !== 'cancelled'){
                    $customizations = [];
                    // get existing customizations
                    foreach($item->customizations as $customization){
                        if($item->id === $customization->item_order_id){
                            $customizations[] = [
                                'id' => $customization->customization->id,
                                'name' => $customization->customization->inventory->name ?? 'No ' . $customization->customization->ingredient->inventory->name,
                                'quantity' => $customization->quantity
                            ];
                        }
                    }
    
                    $this->orders[] = [
                        'uid' => $item->id,
                        'menu_item_id' => $item->menu_item_id,
                        'menu_name' => $item->item->name,
                        'customizations' => $customizations, 
                        'quantity' =>  $item->quantity
                    ];
                }
            }
        }
        elseif($orders->isEmpty()){
            $this->order = Order::create([
                'table_id' => $this->table->id
            ]);
        }
        else{
            dd('Error: there are two existing orders for this table');
        }
    }
    public function switchTabs($id){
        if($id !== ''){
            $this->category = $id;
        }
        else{
            $this->category = '';
        }
    }
    public function openCustomizationModal($id){
        $this->menu = MenuItem::with(['category','ingredients', 'customizations'])->findOrFail($id);
        $this->ordersToAdd['menu_item_id'] = $this->menu->id; 
        foreach($this->menu->ingredients as $ingredient){
            $this->ordersToAdd['ingredients'][$ingredient->id] = 'default';
        }
        foreach($this->menu->customizations as $customization){
            if($customization->action == 'add'){
                $this->ordersToAdd['additionalIngredients'][$customization->id] = 0;
            }
        }
        $this->ordersToAdd['quantity'] = 1;
        $this->showCustomizationModal = true;
    }
    public function closeCustomizationModal(){
        $this->reset(['ordersToAdd', 'menu']);
        $this->showCustomizationModal = false;
    }
    public function addToOrders(){
        $customizationsArray = [];
        foreach($this->ordersToAdd['ingredients'] as $key => $orderIngredients){
            if($orderIngredients !== 'default'){
                foreach($this->customizations as $custom){
                    if($custom->id == $orderIngredients && $custom->action == 'replace'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => $custom->inventory->name,
                            'quantity' => 0
                        ];
                    }
                    elseif($custom->id == $orderIngredients && $custom->action == 'remove'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => 'No ' . $custom->ingredient->inventory->name,
                            'quantity' => 0
                        ];
                    }
                }
            }
        }
        if(array_key_exists('additionalIngredients', $this->ordersToAdd)){
            foreach($this->ordersToAdd['additionalIngredients'] as $key => $additional){
                foreach($this->customizations as $custom){
                    if($custom->id == $key && $additional > 0){
                        $customizationsArray[] = [
                            'id' => $key,
                            'name' => $custom->inventory->name,
                            'quantity' => $additional
                        ];
                    }
                }
            }
        }
        $this->orders[] = [
            'uid' => (string) Str::uuid(),
            'menu_item_id' => $this->menu->id,
            'menu_name' => $this->menu->name,
            'customizations' => $customizationsArray, 
            'quantity' => $this->ordersToAdd['quantity']
        ];

        $this->reset(['ordersToAdd', 'menu']);
        $this->showCustomizationModal = false;
    }
    public function removeOrders($index){
        unset($this->orders[$index]);
        array_values($this->orders);
    }
    public function saveOrders(){
        DB::beginTransaction();
        try{
            $existingIds = [];

            $order = $this->order;

            //Differentiate existing order items and not

            //create item orders ( for loop)
            foreach($this->orders as $itemOrder){
                if(!ItemOrder::where('id', $itemOrder['uid'])->exists()){
                    $ItemOrder = ItemOrder::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $itemOrder['menu_item_id'],
                        'status' => 'pending',
                        'quantity' => $itemOrder['quantity']
                    ]);
                    $existingIds[] = $ItemOrder->id;
                    // create item order customizations ( for loop)
                    foreach($itemOrder['customizations'] as $customization){
                        ItemOrderCustomization::create([
                            'item_order_id' => $ItemOrder->id,
                            'menu_item_customization_id' => $customization['id'],
                            'quantity' => $customization['quantity'] ?: 1
                        ]);
                    }

                    event(new OrderSaved(
                        $ItemOrder->load(
                            // table code
                            'order.table:id,table_code',
                            // menu item name
                            'item:id,name',
                            // item order customizations
                            'customizations:id,item_order_id,menu_item_customization_id,quantity',
                            // menu customizations
                            'customizations.customization:id,ingredient_id,inventory_id,action',
                            // inventory
                            'customizations.customization.ingredient.inventory:id,name',
                            'customizations.customization.inventory:id,name',
                        )
                    ));
                }
                else{
                    $existingIds[] = $itemOrder['uid'];
                }
            }
            $databaseIds = ItemOrder::where('order_id', $order->id)->pluck('id')->toArray();
            $missingIds = array_diff($databaseIds, $existingIds);

            $itemsToUpdate = ItemOrder::whereIn('id', $missingIds)
                ->where('status', '!=', 'cancelled')
                ->get();
            foreach($itemsToUpdate as $item){
                $item->update(['status' => 'cancelled']);

                event(new OrderCancelled($item->id, $this->table->table_code));
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }
    }
    public function render()
    {
        $active = $this->category;
        $categories = MenuItemCategory::all();
        $menuItems = $this->category ? 
            MenuItem::with(['category','ingredients', 'customizations'])
                ->where('menu_item_category_id', $this->category)
                ->paginate(10)
            :
            MenuItem::with(['category','ingredients', 'customizations'])
                ->paginate(10);

        return view('livewire.order.create')
            ->with(compact('active', 'categories', 'menuItems'));
    }

    // add status to item orders for tracking whether the item is order pending, preparing, completed, or cancled(maybe)
    // add price computation for saved orders
    //
}
