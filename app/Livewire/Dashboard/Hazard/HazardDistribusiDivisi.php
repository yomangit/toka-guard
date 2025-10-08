<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;

class HazardDistribusiDivisi extends Component
{
    public $categories; // nama department atau contractor


    public function mount()
    {
        // Ambil semua hazard beserta relasi
        $year = Carbon::now()->year;
        $hazards = Hazard::with(['department', 'contractor'])->whereYear('tanggal', Carbon::now()->year)->get();

        // Kumpulkan kategori (nama department jika ada, kalau kosong pakai contractor)
        $grouped = $hazards->groupBy(function ($hazard) {
            if ($hazard->department) {
                return $hazard->department->department_name;
            } elseif ($hazard->contractor) {
                return $hazard->contractor->contractor_name;
            } else {
                return 'Tidak Diketahui';
            }
        });

        // Hitung jumlah per kategori dan urutkan dari terbesar ke terkecil
        $counts = $grouped->map->count()->sortDesc();
        // Hitung jumlah per kategori
        $value = [
            'year' => $year,
            'label'  => $counts->keys()->values()->toArray(),   // urutan label mengikuti sortDesc()
            'counts' => $counts->values()->toArray(),            // urutan data sesuai label

        ];
        $this->categories = json_encode($value);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-distribusi-divisi');
    }
}
