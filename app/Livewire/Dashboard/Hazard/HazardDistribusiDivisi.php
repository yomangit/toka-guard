<?php

namespace App\Livewire\Dashboard\Hazard;

use App\Models\Hazard;
use Livewire\Component;

class HazardDistribusiDivisi extends Component
{
    public $categories; // nama department atau contractor


    public function mount()
    {
        // Ambil semua hazard beserta relasi
        $hazards = Hazard::with(['department', 'contractor'])->get();

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

        // Hitung jumlah per kategori
        $value = [
            'label' => $grouped->keys()->toArray(),
            'counts' => $grouped->map->count()->values()->toArray()
        ];
         $this->categories = json_encode($value);
    }
    public function render()
    {
        return view('livewire.dashboard.hazard.hazard-distribusi-divisi');
    }
}
