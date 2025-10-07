<?php

namespace App\Livewire\Dashboard\Hazard;

use Livewire\Component;
use App\Models\Hazard as ModelsHazard;
use Carbon\Carbon;

class HazardTrandChart extends Component
{
    public $data;
    public function mount()
    {
        $data = ModelsHazard::selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        $data = [
           'months' => $data->pluck('month')->map(function ($m) {
                return Carbon::create()->month($m)->format('M');
            })->toArray(),
            'counts' => $data->pluck('total')->toArray()
        ];
         $this->data = json_encode($data);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-trand-chart');
    }
}
