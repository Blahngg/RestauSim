<?php

namespace App\Livewire\Kitchen;

use App\Models\ItemOrder;
use App\Models\Order;
use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class KitchenDashboard extends Component
{
    public $orders = [];
    public function mount(){
        $tables = Table::all();
        $orders = Order::with('items', 'table')->get();
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
