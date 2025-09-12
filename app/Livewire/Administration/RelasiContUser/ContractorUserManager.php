<?php

namespace App\Livewire\Administration\RelasiContUser;

use App\Models\User;
use Livewire\Component;
use App\Models\Contractor;

class ContractorUserManager extends Component
{
     public $contractor_id;
    public $user_id;
    public $contractors = [];
    public $users = [];
    public $selectedUsers = [];

    public $searchContractor = '';
    public $searchUser = '';

    public $contractor_name;
    public $showContractorDropdown = false;
    public $showOnlySelected = false;

    public function updatedSearchContractor()
    {
        $this->contractors = Contractor::where('contractor_name', 'like', '%' . $this->searchContractor . '%')
            ->orderBy('contractor_name')
            ->limit(10)
            ->get();
    }

    public function selectContractor($id, $name)
    {
        $this->contractor_id = $id;
        $this->contractor_name = $name;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;

        // load user yg sudah terkait
        $this->selectedUsers = Contractor::find($id)->users()->pluck('user_id')->toArray();
    }

    public function updatedSearchUser()
    {
        if ($this->contractor_id) {
            $this->users = User::search(trim($this->searchUser))->get();
        }
    }

    public function toggleUser($id)
    {
        if (in_array($id, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$id]);
        } else {
            $this->selectedUsers[] = $id;
        }
    }

    public function save()
    {
        $contractor = Contractor::find($this->contractor_id);
        $contractor->users()->sync($this->selectedUsers);

        $this->dispatch('alert', [
            'text' => 'Relasi contractor-user berhasil disimpan!',
            'duration' => 5000,
            'close' => true,
        ]);
    }
    public function render()
    {
        $this->updatedSearchUser();
        return view('livewire.administration.relasi-cont-user.contractor-user-manager');
    }
}
