<?php

namespace App\Livewire\Dashboard;

use App\Models\Hazard as ModelsHazard;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Hazard extends Component
{
    public $total_hazard;
    public $range_date = '';
    public $start_date;
    public $end_date;
    public function updatedRangeDate($value)
    {
        // Cek apakah nilai tidak kosong
        if (!empty($value)) {
            // Pisahkan string berdasarkan " to "
            $dates = explode(' to ', $value);

            // Pastikan ada dua tanggal yang valid
            if (count($dates) === 2) {
                $this->start_date = $dates[0];
                $this->end_date = $dates[1];
            }
        } else {
            $this->reset('start_date', 'end_date');
        }
    }
    public function render()
    {
        $totalHazard = ModelsHazard::count();

        $hazardByStatus = ModelsHazard::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        return view('livewire.dashboard.hazard', [
            'totalHazard' => $totalHazard,
            'hazardByStatus' => $hazardByStatus
        ]);
    }
}
