<?php

namespace App\Events;

use App\Models\ItemOrder;
use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderSaved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ItemOrder $itemOrder)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('kitchen-orders'),
        ];
    }

    public function broadcastAs()
    {
        return 'order.saved';
    }

    public function broadcastWith()
    {
        // $customizations = [];
        // foreach($this->itemOrder->customizations as $custom){
        //     if($custom->customization->action == 'remove'){
        //         $customizations[] = [
        //             'id' => $custom->customization->id,
        //             'name' => 'No ' . $custom->customization->ingredient->inventory->name,
        //             'quantity' => $custom->quantity,
        //         ];
        //     }
        //     else if($custom->customization->action == 'replace'){
        //         $customizations[] = [
        //             'id' => $custom->customization->id,
        //             'name' => $custom->customization->inventory->name,
        //             'quantity' => $custom->quantity,
        //         ];
        //     }
        //     else{
        //         $customizations[] = [
        //             'id' => $custom->customization->id,
        //             'name' => 'Extra ' . $custom->customization->inventory->name,
        //             'quantity' => $custom->quantity,
        //         ];
        //     }
        // }
        // dd($customizations);
        // foreach($this->itemOrder->customizations as $custom) {
        //     $customization = $custom->customization;
        //     dd([
        //         'id' => $customization->id,
        //         'action' => $customization->action,
        //         'ingredient_id' => $customization->ingredient_id,
        //         'inventory_id' => $customization->inventory->name,
        //     ]);
        // }

        return [
            'table_code' => $this->itemOrder->order->table->table_code,
            'item_id' => $this->itemOrder->id,
            'item_name' => $this->itemOrder->item->name,
            'status' => $this->itemOrder->status,
            'quantity' => $this->itemOrder->quantity,
            'customizations' => $this->itemOrder->customizations->map(function ($custom) {
            $customization = $custom->customization;

            if (! $customization) {
                return null;
            }

            return [
                'id' => $customization->id,
                'name' => match ($customization->action) {
                    'remove'  => 'No ' . $customization->ingredient->inventory->name,
                    'replace' => $customization->inventory->name,
                    default   => 'Extra ' . $customization->inventory->name . ' ' . $custom->quantity . 'x',
                },
                'quantity' => $custom->quantity,
            ];
        })->filter()->values(),
        ];
    }
}
