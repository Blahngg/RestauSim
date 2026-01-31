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
use ParagonIE\Sodium\Core\Curve25519\Ge\P2;

#[Layout('layouts.app')]
class Create extends Component
{
    public $order;
    public $category;
    public $table;
    public $menuItems;
    public $customizations;
    public $showCustomizationModal = false;
    public $showDiscountModal = false;
    public $menu;
    public $ordersToAdd = [];
    public $orders = [];
    public $orderCount = 0;
    // public $itemDiscount = [];
    public function mount(Table $table){
        $this->table = $table;
        $this->customizations = MenuItemCustomization::with(['ingredient', 'inventory', 'unitOfMeasurement'])->get();
        $this->menuItems = MenuItem::with(['category','ingredients', 'customizations'])->get();

        // dd($this->customizations);

        //Search for existing orders
        $orders = Order::with(['items'])->where('table_id', $this->table->id)->limit(2)->get();
        if($orders->count() === 1){
            $this->order = $orders->first();
            // initialize existing order items
            foreach($this->order->items as $item){
                if($item->status !== 'cancelled'){
                    $customizations = [];
                    $totalPrice = (int) $item->item->price;
                    // get existing customizations
                    foreach($item->customizations as $customization){
                        if($item->id === $customization->item_order_id){
                            $customizations[] = [
                                'id' => $customization->customization->id,
                                'name' => $customization->customization->inventory->name ?? 'No ' . $customization->customization->ingredient->inventory->name,
                                'quantity' => $customization->customization->quantity_used,
                                'price' => $customization->customization->price,
                            ];
                            $totalPrice += (int) $customization->customization->price;
                        }
                    }

                    $computedPrice = $customization->customization->quantity_used * $totalPrice;
    
                    $this->orders[] = [
                        'uid' => $item->id,
                        'menu_item_id' => $item->menu_item_id,
                        'menu_name' => $item->item->name,
                        'customizations' => $customizations, 
                        'quantity' =>  $item->quantity,
                        'base_price' => $totalPrice,
                        'price' => $computedPrice,
                    ];
                }
            }
        }
        elseif($orders->isEmpty()){
            $this->order = Order::create([
                'table_id' => $this->table->id,
                'subtotal_amount' => 0,
                'total_discount_amount' => 0,
                'total_vat_amount' => 0,
                'total_amount' => 0,
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
    public function incrementAdditionalIngredient($index){
        ++$this->ordersToAdd['additionalIngredients'][$index];
    }
    public function decrementAdditionalIngredient($index){
        if($this->ordersToAdd['additionalIngredients'][$index] > 0){
            --$this->ordersToAdd['additionalIngredients'][$index];
        }
    }
    public function incrementOrderQuantity(){
        ++$this->ordersToAdd['quantity'];
    }
    public function decrementOrderQuantity(){
        if($this->ordersToAdd['quantity'] > 1){
            --$this->ordersToAdd['quantity'];
        }
    }
    public function addToOrders(){
        $customizationsArray = [];
        $totalPrice = (int) $this->menu->price;
        foreach($this->ordersToAdd['ingredients'] as $key => $orderIngredients){
            if($orderIngredients !== 'default'){
                foreach($this->customizations as $custom){
                    if($custom->id == $orderIngredients && $custom->action == 'replace'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => $custom->inventory->name,
                            'quantity' => 0,
                            'price' => $custom->price,
                        ];
                        $totalPrice += (int) $custom->price;
                    }
                    elseif($custom->id == $orderIngredients && $custom->action == 'remove'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => 'No ' . $custom->ingredient->inventory->name,
                            'quantity' => 0,
                            'price' => $custom->price,
                        ];
                        $totalPrice += (int) $custom->price;
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
                            'quantity' => $additional,
                            'price' => (int) $custom->price,
                        ];
                        $totalPrice += $custom->price * $additional;
                    }
                }
            }
        }

        $computedPrice = $this->ordersToAdd['quantity'] * $totalPrice;

        $this->orders[] = [
            'uid' => (string) Str::uuid(),
            'menu_item_id' => $this->menu->id,
            'menu_name' => $this->menu->name,
            'customizations' => $customizationsArray, 
            'quantity' => $this->ordersToAdd['quantity'],
            'base_price' => $totalPrice,
            'price' => $computedPrice,
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
                        'quantity_ordered' => $itemOrder['quantity'],
                        'price_at_sale' => $itemOrder['base_price'],
                        'vat_rate' => 12,
                        'vat_removed_amount' => 0,
                        'discount_percentage' => 0,
                        'discount_amount' => 0,
                        'net_amount' => 0,
                    ]);
                    $existingIds[] = $ItemOrder->id;
                    // create item order customizations ( for loop)
                    foreach($itemOrder['customizations'] as $customization){
                        ItemOrderCustomization::create([
                            'item_order_id' => $ItemOrder->id,
                            'menu_item_customization_id' => $customization['id'],
                            'quantity_ordered' => $customization['quantity'] ?: 1
                        ]);
                    }

                    event(new OrderSaved(
                        $ItemOrder->load(
                            // table code
                            'order.table:id,table_code',
                            // menu item name
                            'item:id,name',
                            // item order customizations
                            'customizations:id,item_order_id,menu_item_customization_id,quantity_ordered',
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

// public function openDiscountModal($index){

//     $this->itemDiscount['name'] = $this->orders[$index]['menu_name'];
//     $this->itemDiscount['base_price'] = $this->orders[$index]['base_price'];
//     $this->showDiscountModal = true;
// }
// public function closeDiscountModal(){
//     $this->showDiscountModal = false;
// }
