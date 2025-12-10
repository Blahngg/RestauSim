<?php

namespace App\Livewire\Order;

use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    public $table;
    public function mount(Table $table){
        $this->table = $table;
    }
    public function render()
    {
        return view('livewire.order.create');
    }
}
