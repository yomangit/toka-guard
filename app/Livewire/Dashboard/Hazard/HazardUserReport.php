<?php

namespace App\Livewire\Dashboard\Hazard;

use Carbon\Carbon;
use App\Models\Hazard;
use Livewire\Component;

class HazardUserReport extends Component
{
    public $pelapor; // nama department atau contractor


    public function mount()
    {
        // Ambil semua hazard beserta relasi
        $year = Carbon::now()->year;
        $hazards = Hazard::with('pelapor')->whereYear('tanggal', Carbon::now()->year)->get();

        // Kumpulkan kategori (nama department jika ada, kalau kosong pakai contractor)
        $grouped = $hazards->groupBy(function ($hazard) {
            if ($hazard->pelapor) {
                return $hazard->pelapor->name;
            }  else {
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
        $this->pelapor = json_encode($value);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-user-report');
    }
}
