<?php

namespace App\Livewire\Dashboard\Hazard;

use App\Models\Hazard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class HazardDistribusiStatus extends Component
{
    public $statusChart;

    public function mount()
    {
        $data = Hazard::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $value = [
            'labels' => $data->pluck('status')->toArray(),
            'values' => $data->pluck('total')->toArray(),
        ];
         $this->statusChart = json_encode($value);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-distribusi-status');
    }
}
