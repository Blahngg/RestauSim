<?php

namespace App\Livewire\Kitchen;

use App\Models\Inventory;
use App\Models\ItemOrder;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Table;
use App\Models\UnitOfMeasurement;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class KitchenDashboard extends Component
{
    public $orders = [];
    public $inventory;
    public $itemOrders;
    public $uom;
    public function mount(){
        $tables = Table::where('floor_plan_id', 3)->get();
        $orders = Order::with('items', 'table')->get();
        $this->inventory = Inventory::with(['inventoryUnit', 'costUnit'])->get();
        $this->itemOrders = ItemOrder::with(['item.ingredients', 'customizations', 'customizations.customization'])->get();
        $this->uom = UnitOfMeasurement::all();
        foreach($tables as $table){
            $this->orders[$table['table_code']] = [];
            foreach($orders as $order){
                if($table->id === $order->table->id){
                    foreach($order->items as $item){
                        if($item->status !== 'cancelled'){
                            $customizations = [];
                            foreach($item->customizations as $custom){
                                if($custom->customization->action == 'remove'){
                                    $customizations[] = [
                                        'id' => $custom->customization->id,
                                        'name' => 'No ' . $custom->customization->ingredient->inventory->name,
                                        'quantity' => $custom->quantity,
                                    ];
                                }
                                else if($custom->customization->action == 'replace'){
                                    $customizations[] = [
                                        'id' => $custom->customization->id,
                                        'name' => $custom->customization->inventory->name,
                                        'quantity' => $custom->quantity,
                                    ];
                                }
                                else{
                                    $customizations[] = [
                                        'id' => $custom->customization->id,
                                        'name' => 'Extra ' . $custom->customization->inventory->name,
                                        'quantity' => $custom->quantity,
                                    ];
                                }
                            }
                            $this->orders[$table['table_code']][] = [
                                'item_id' => $item->id,
                                'item_name' => $item->item->name,
                                'quantity' => $item->quantity,
                                'status' => $item->status,
                                'customizations' => $customizations,
                            ];
                        }
                    }
                }
            }
        }
        // dd($this->orders);
    }
    #[On('echo-private:kitchen-orders,.order.saved')]
    public function addOrder($itemOrder){
        $table = $itemOrder['table_code'];
        unset($itemOrder['table_code']);
        $this->orders[$table][] = $itemOrder;
    }
    #[On('echo-private:kitchen-orders,.order.cancelled')]
    public function markCancelled($data){
        // foreach ($this->orders as $table => &$items) {
        //     $items = array_filter($items, fn($item) => $item['item_id'] !== $missing);

        //     // reindex so Livewire updates correctly
        //     $items = array_values($items);
        // }

        $this->orders[$data['table']] = array_values(
            array_filter(
                $this->orders[$data['table']],
                fn ($item) => $item['item_id'] !== $data['item']
            )
        );

        unset($items); // good practice
    }
    public function updateStatus($itemOrder, $status, $table, $index){
        if($status == 'pending'){
            $item = $this->itemOrders->find($this->orders[$table][$index]['item_id']);

            $ingredientsToDeduct = [];
            $customizationsToDeduct = [];

            foreach($item->item->ingredients as $ingredient){
                $ingredientsToDeduct[] = [
                    'ingredient_id' => $ingredient->id,
                    'inventory_id' => $ingredient->inventory_id,
                    'quantity_used' => $ingredient->quantity_used,
                    'unit_of_measurement' => $ingredient->unit_of_measurement_id,
                ];
            }

            foreach($item->customizations as $custom){
                $customizationsToDeduct[] = [
                    'ingredient_id' => $custom->customization->ingredient_id ?? null,
                    'inventory_id' => $custom->customization->inventory_id ?? null,
                    'quantity_used' => $custom->customization->quantity_used * $custom->quantity_ordered ?? 0,
                    'unit_of_measurement' => $custom->customization->unit_of_measurement_id ?? null
                ];
            }

            $customizationsIngredientIds = array_column($customizationsToDeduct, 'ingredient_id');

            $filteredIngredients = array_filter($ingredientsToDeduct, function ($item) use ($customizationsIngredientIds) {
                return !in_array($item['ingredient_id'], $customizationsIngredientIds);
            });

            $filteredIngredients = array_values($filteredIngredients);

            // dd($ingredientsToDeduct, $customizationsToDeduct, $filteredIngredients);

            foreach($customizationsToDeduct as $customDeduct){
                if($customDeduct['quantity_used'] > 0){
                    DB::beginTransaction();
                    try{
                        $inventoryToDeduct = Inventory::lockForUpdate()->find($customDeduct['inventory_id']);
                        $unitOfMeasurement = $this->uom->find($customDeduct['unit_of_measurement']);

                        $inventoryToDeduct->deductQuantityOnHand(
                            $customDeduct['quantity_used'], 
                            $unitOfMeasurement->symbol, 
                            $unitOfMeasurement->category
                        );
                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }
            }

            foreach($filteredIngredients as $ingredientDeduct){
                if($ingredientDeduct['quantity_used'] > 0){
                    DB::beginTransaction();
                    try{
                        $inventoryToDeduct = Inventory::lockForUpdate()->find($ingredientDeduct['inventory_id']);
                        $unitOfMeasurement = $this->uom->find($ingredientDeduct['unit_of_measurement']);

                        $inventoryToDeduct->deductQuantityOnHand(
                            $ingredientDeduct['quantity_used'], 
                            $unitOfMeasurement->symbol, 
                            $unitOfMeasurement->category
                        );
                        DB::commit();
                    }catch(Exception $e){
                        DB::rollBack();
                    }
                }
            }

            ItemOrder::findOrFail($itemOrder)->update(['status' => 'preparing']);
            $this->orders[$table][$index]['status'] = 'preparing';
        }
        elseif($status == 'preparing'){
            ItemOrder::findOrFail($itemOrder)->update(['status' => 'completed']);
            $this->orders[$table][$index]['status'] = 'completed';
        }
    }
    public function cancelItem(int $itemOrder, $table){
        // dd($itemOrder, $table);
        ItemOrder::findOrFail($itemOrder)->update(['status' => 'cancelled']);

        $this->orders[$table] = array_values(
            array_filter(
                $this->orders[$table],
                fn ($item) => $item['item_id'] !== $itemOrder
            )
        );
    }
    public function render()
    {
        return view('livewire.kitchen.kitchen-dashboard');
    }
}
