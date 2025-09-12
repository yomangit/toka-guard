<?php

namespace App\Livewire\Administration\EventGeneral;

use Flux\Flux;
use App\Models\User;
use App\Models\Company;
use App\Models\Contractor;
use Livewire\Component;
use App\Models\Department;
use App\Models\ErmAssignment;
use Livewire\Attributes\Validate;

class ErmAssignmentManager extends Component
{
    #[Validate('required', message: 'Harus Pilih User ERM!!!')]
    public $selectedUsers = [];
    public $selectedDepartment = null, $selectedCompany = null;
    public $search_user = '', $delete_id, $name, $erm_id, $legend, $user_id;
    public function open_modal()
    {
        $this->legend              = 'Tambah ERM';
        Flux::modal('ERM-Asign')->show();
    }
    public function open_modal_edit(ErmAssignment $id)
    {
        $this->erm_id = $id->id;
        Flux::modal('ERM-edit')->show();
        if ($id->id) {
            $this->selectedDepartment = $id->department_id;
            $this->selectedCompany    = $id->contractor_id;
            $this->user_id              = $id->user_id;
        }
    }
    public function close_modal()
    {
        if ($this->erm_id) {
            Flux::modal('ERM-edit')->close();
        } else {
            Flux::modal('ERM-Asign')->close();
        }
        $this->reset('selectedUsers', 'selectedCompany', 'selectedDepartment');
    }
    public function assign()
    {
        $this->validate();
        foreach ($this->selectedUsers as $userId) {
            ErmAssignment::updateOrCreate([
                'user_id' => $userId,
                'department_id' => $this->selectedDepartment,
                'contractor_id' => $this->selectedCompany,
            ]);
        }
        $this->dispatch(
            'alert',
            [
                'text'            => 'ERM berhasil ditetapkan.',
                'duration'        => 5000,
                'destination'     => '/contact',
                'newWindow'       => true,
                'close'           => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]
        );
        $this->selectedUsers = [];
    }
    public function update()
    {
        $this->validate(['user_id' => 'required',], [
            'user_id' => 'user harus di isi'
        ]);
        ErmAssignment::where('id', $this->erm_id)->update([
            'user_id' => $this->user_id,
            'department_id' => $this->selectedDepartment,
            'contractor_id' => $this->selectedCompany,
        ]);
        $this->dispatch(
            'alert',
            [
                'text'            => 'ERM berhasil diperbaharui.',
                'duration'        => 5000,
                'destination'     => '/contact',
                'newWindow'       => true,
                'close'           => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]
        );
    }
    public function showDelete(ErmAssignment $id)
    {
        Flux::modal('delete-erm')->show();
        $this->delete_id           = $id->id;
        $this->name = $id->user->name;
    }
    public function delete()
    {
        $deleteFile = ErmAssignment::whereId($this->delete_id);
        $deleteFile->delete();
        Flux::modal('delete-erm')->close();
        $this->dispatch(
            'alert',
            [
                'text'            => "Data berhasil di hapus!!!",
                'duration'        => 5000,
                'destination'     => '/contact',
                'newWindow'       => true,
                'close'           => true,
                'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
            ]
        );
    }
    public function render()
    {
        return view('livewire.administration.event-general.erm-assignment-manager', [
            'departments' => Department::all(),
            'contractors' => Contractor::all(),
            'users' => User::search(trim($this->search_user))->get(),
            'ErmAssignment' => ErmAssignment::paginate(20)
        ]);
    }
}
