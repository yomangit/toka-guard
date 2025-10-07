<?php

namespace App\Livewire\Dashboard;

use App\Models\Hazard as ModelsHazard;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Hazard extends Component
{
    public function render()
    {
            $year = date('Y');

        // Ambil jumlah laporan per bulan berdasarkan kolom `tanggal`
        $data = ModelsHazard::select(
                DB::raw('MONTH(tanggal) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('tanggal', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Label bulan
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $labels = [];
        $values = [];
        foreach ($months as $num => $label) {
            $labels[] = $label;
            $values[] = $data[$num] ?? 0;
        }
        return view('livewire.dashboard.hazard',compact('labels', 'values', 'year'));
    }
}
