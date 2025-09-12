<?php

namespace App\Livewire\Hazard;

use App\Models\Hazard;
use Livewire\Component;

class HazardList extends Component
{
    public function render()
    {
         $hazards = Hazard::with('eventSubType','department','contractor')->latest()->get();
        return view('livewire.hazard.hazard-list', compact('hazards'));
    }
}
