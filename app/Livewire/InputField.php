<?php

namespace App\Livewire;

use Livewire\Component;

class InputField extends Component
{
    public $model;        // properti Livewire parent (misal: location_specific)
    public $label;        // label input
    public $placeholder;  // placeholder
    public $required = false; // apakah field wajib diisi
    public $type = 'text';    // tipe input default
    public $id;               // id input

    public function mount($model, $label = '', $placeholder = '', $type = 'text', $required = false, $id = null)
    {
        $this->model = $model;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->type = $type;
        $this->required = $required;
        $this->id = $id ?? $model;
    }
    public function render()
    {
        return view('livewire.input-field');
    }
}
