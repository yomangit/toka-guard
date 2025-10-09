<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Hazard as ModelsHazard;

class HazardTrandChart extends Component
{
    public $data;
    public $start_date;
    public $end_date;
    #[On('dateRangeUpdated')]
    public function updateDateRange($data)
    {
        $this->start_date = $data['start'];
        $this->end_date   = $data['end'];

        // 🔁 Misalnya langsung panggil refresh data
        $this->loadData();
    }
    #[On('chartUpdated')]
    public function loadData()
    {
        $data = ModelsHazard::selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
            ->whereYear('tanggal', Carbon::now()->year)
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
        $this->dispatch('trandChart', $this->data);
    }
    public function render()
    {
        $this->loadData();
        return view('livewire.dashboard.hazard.hazard-trand-chart');
    }
}
