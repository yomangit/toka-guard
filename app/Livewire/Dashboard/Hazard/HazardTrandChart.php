<?php

namespace App\Livewire\Dashboard\Hazard;

use Livewire\Component;
use App\Models\Hazard as ModelsHazard;
use Carbon\Carbon;

class HazardTrandChart extends Component
{
    public $months = [];
    public $counts = [];
    public function mount()
    {
        $data = ModelsHazard::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $this->months = $data->pluck('month')->map(function ($m) {
            return Carbon::create()->month($m)->format('M');
        });
        $this->counts = $data->pluck('total');
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-trand-chart');
    }
}
