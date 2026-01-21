<?php

namespace App\Livewire\FloorPlan;

use App\Models\FloorPlan;
use App\Models\Table;
use Livewire\Attributes\Layout;
use Livewire\Component;
#[Layout('layouts.app')]
class ViewFloorPlan extends Component
{
    public $floorplan;
    public $tables = [];
    public $currentTable;
    public $showSideBar = false;
    public function mount(){
        $this->floorplan = FloorPlan::findOrFail(1);
        $this->tables = $this->floorplan->tables->keyBy('svg_id');
    }
    public function toggleTable($svgId){
        $table = Table::where(['floor_plan_id' => $this->floorplan->id, 'svg_id' => $svgId])->first();
        if($this->currentTable){
            if($this->currentTable->table_code == $table->table_code){
                $this->showSideBar = false;
                $this->currentTable = NULL;
            }
            else{
                $this->currentTable = $table;
            }
        }
        else{
            $this->showSideBar = true;
            $this->currentTable = $table;
        } 
    }
    public function render()
    {
        $floorplan = $this->floorplan;
        $tables = $this->tables;
        $currentTable = $this->currentTable;

        return view('livewire.floor-plan.view-floor-plan')
            ->with(compact('floorplan', 'tables', 'currentTable'));
    }
}
