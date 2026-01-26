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
    public $showDiscountModal = false;
    public $index;
    public $menu;
    public $ordersToAdd = [];
    public $orders = [];
    public $orderCount = 0;
    public $itemDiscount = [
        'index' => null,
        'uid' => null,
        'name' => null,
        'base_price' => null,
        'discount_type' => '', 
        'discount_value' => null,
        'discount_amount' => 0,
        'vat_exempt' => [],
        'id_number' => null,
    ];
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

                    $price = $item->quantity * $item->item->price;
    
                    $this->orders[] = [
                        'uid' => $item->id,
                        'menu_item_id' => $item->menu_item_id,
                        'menu_name' => $item->item->name,
                        'customizations' => $customizations, 
                        'quantity' =>  $item->quantity,
                        'price' => $price,
                    ];
                }
            }
        }
        elseif($orders->isEmpty()){
            // $this->order = Order::create([
            //     'table_id' => $this->table->id
            // ]);
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
    public function openDiscountModal($index, $uid){
        $this->showDiscountModal = true;
        $this->itemDiscount['uid'] = $uid;
        $this->itemDiscount['index'] = $index;
        $this->itemDiscount['name'] = $this->orders[$index]['menu_name'];
        $this->itemDiscount['base_price'] = $this->orders[$index]['unit_price'];
        $this->index = $index;
    }
    public function closeDiscountModal(){
        $this->showDiscountModal = false;
        $this->reset('itemDiscount', 'index');
    }
    public function updatedItemDiscountDiscountType($type){
        if ($type !== 'custom') {
            $this->itemDiscount['discount_value'] = null;
        }

        if (!in_array($type, ['senior', 'pwd'])) {
            $this->itemDiscount['id_number'] = null;
            $this->itemDiscount['vat_exempt'] = null;
        }

        if (in_array($type, ['senior', 'pwd'])) {
            $this->itemDiscount['vat_exempt'][] = 'true';
        }
    }
    public function updatedItemDiscountDiscountValue($value){
        if($value > 0 && $value <= 100){
            $this->itemDiscount['discount_value'] = $value;
            $this->itemDiscount['discount_amount'] = $this->itemDiscount['base_price'] * ($value / 100);
        }
    }
    public function updatedItemDiscountIdNumber($value){
        if($value){
            $this->itemDiscount['discount_value'] = 20;
            $this->itemDiscount['discount_amount'] = $this->itemDiscount['base_price'] * 0.2;
        }
    }
    public function addToOrders(){
        $customizationsArray = [];
        $customizationsPrice = 0;
        foreach($this->ordersToAdd['ingredients'] as $key => $orderIngredients){
            if($orderIngredients !== 'default'){
                foreach($this->customizations as $custom){
                    if($custom->id == $orderIngredients && $custom->action == 'replace'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => $custom->inventory->name,
                            'quantity' => 0,
                            'price' => $custom->price
                        ];
                        $customizationsPrice =+ $custom->price;
                    }
                    elseif($custom->id == $orderIngredients && $custom->action == 'remove'){
                        $customizationsArray[] = [
                            'id' => $orderIngredients,
                            'name' => 'No ' . $custom->ingredient->inventory->name,
                            'quantity' => 0,
                            'price' => $custom->price
                        ];
                        $customizationsPrice =+ $custom->price;
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
                            'price' => $custom->price
                        ];
                        $customizationsPrice =+ $custom->price;
                    }
                }
            }
        }

        // dd($this->menu->base_price,$customizationsPrice);
        $total = null;
        if(!$this->menu->is_vat_inclusive){
            $taxable_amount = (int) $this->menu->base_price + $customizationsPrice;
        }
        else{
            $base_price = $this->menu->base_price / (1 + $this->menu->vat_rate);
            $taxable_amount =  $base_price + $customizationsPrice;
        }
            
        $vat = $taxable_amount * $this->menu->vat_rate;
        $total = $taxable_amount + $vat;

        $this->orders[] = [
            'uid' => (string) Str::uuid(),
            'menu_item_id' => $this->menu->id,
            'menu_name' => $this->menu->name,
            'customizations' => $customizationsArray, 
            'quantity' => $this->ordersToAdd['quantity'],
            'unit_price' => $this->menu->base_price,
            'discount_type' => null, 
            'discount_value' => null, // how much is the discount
            'discount_amount' => null, // computed from discount value
            'vat_rate' => $this->menu->vat_rate, // from menu items
            'subtotal' => $taxable_amount, // after discount, before vat
            'total' => $total,  // final amount, including vat
        ];

        $this->reset(['ordersToAdd', 'menu']);
        $this->showCustomizationModal = false;
    }
    public function removeOrders($index){
        unset($this->orders[$index]);
        array_values($this->orders);
    }
    public function saveDiscountData(){
        dd($this->itemDiscount);
        if($this->orders[$index]['uid'] === $uid){
            $this->orders[$index]['discount_type'] = null;
            $this->orders[$index]['discount_value'] = null;
            $this->orders[$index]['discount_amount'] = null;
            $this->orders[$index]['subtotal'] = null;
            $this->orders[$index]['total'] = null;

        }
    }
    public function saveOrders(){
        DB::beginTransaction();
        try{
            $existingIds = [];

            $order = $this->order ?? Order::create([
                'table_id' => $this->table->id,
                'code' => strtoupper(Str::random(8)),
                'subtotal' => 0,
                'discount_total' => 0,
                'vat_total' => 0,
                'service_charge' => 0,
                'grand_total' => 0,
            ]);

            //Differentiate existing order items and not

            //create item orders ( for loop)
            foreach($this->orders as $itemOrder){
                if(!ItemOrder::where('id', $itemOrder['uid'])->exists()){
                    $ItemOrder = ItemOrder::create([
                        'order_id' => $order->id,
                        'menu_item_id' => $itemOrder['menu_item_id'],
                        'status' => 'pending',
                        'quantity' => $itemOrder['quantity'],
                        'unit_price' => 0, // from menu items
                        'discount_type' => '', 
                        'discount_value' => '', // how much is the discount
                        'discount_amount' => '', // computed from discount value
                        'vat_rate' => '', // from menu items
                        'subtotal' => '', // after discount, before vat
                        'total' => '',  // final amount, including vat
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
