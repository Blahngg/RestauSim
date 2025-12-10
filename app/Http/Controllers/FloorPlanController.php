<?php

namespace App\Http\Controllers;

use App\Models\FloorPlan;
use App\Models\Table;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FloorPlanController extends Controller
{
    public function create(){
        return view('floor-plan-create');
    }

    public function store(Request $request){
        $validated = $request->validate([
            'name' => 'required',
            'filepath' => 'required|file|mimes:svg'
        ]);

        DB::beginTransaction();
        try{
            $validated['filepath'] = $validated['filepath']->store('floor_plans', 'public');
            $floorplan = FloorPlan::create($validated);

            $svg = file_get_contents('storage/' . $floorplan->filepath);

            preg_match_all('/data-cell-id="(table-.*?)"/', $svg, $matches);

            foreach($matches[1] as $id){
                Table::firstOrCreate([
                    'floor_plan_id' => $floorplan->id,
                    'svg_id' => $id,
                    'table_code' => strtoupper($id),
                ]);
            }

            DB::commit();
        }catch(Exception $e){
            DB::rollBack();
            dd($e);
        }

        return to_route('floorplan.create');
    }
}
