<?php

namespace App\Livewire\Dashboard;

use App\Models\Hazard as ModelsHazard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hazard extends Component
{
    public $total_hazard;

    public function render()
    {
        $totalHazard = ModelsHazard::count();

    $hazardByStatus = ModelsHazard::select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();
        return view('livewire.dashboard.hazard',[
            'totalHazard'=>$totalHazard,
            'hazardByStatus' => $hazardByStatus
        ]);
    }
}
