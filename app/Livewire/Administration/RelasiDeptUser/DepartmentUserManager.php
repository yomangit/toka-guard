<?php

namespace App\Livewire\Administration\RelasiDeptUser;

use App\Models\User;
use Livewire\Component;
use App\Models\Department;

class DepartmentUserManager extends Component
{
    public $department_id;
    public $user_id;
    public $departments = [];
    public $users = [];
    public $selectedUsers = [];

    public $searchDepartment = '';
    public $searchUser = '';

    public $department_name;
    public $showDepartmentDropdown = false;
    public $showOnlySelected = false;

    public function updatedSearchDepartment()
    {
        $this->departments = \App\Models\Department::where('department_name', 'like', '%' . $this->searchDepartment . '%')
            ->orderBy('department_name')
            ->limit(10)
            ->get();
    }

    public function selectDepartment($id, $name)
    {
        $this->department_id = $id;
        $this->department_name = $name;
        $this->searchDepartment = $name; // tampilkan nama di input
        $this->showDepartmentDropdown = false;
        // Pilih department → load user yang sudah terkait
        $this->selectedUsers = Department::find($id)->users()->pluck('user_id')->toArray();
    }
    public function updateSearchUser()
    {
        if ($this->department_id) {
            $this->users = User::search(trim($this->searchUser))->get();
        }
    }

    // Toggle user di selectedUsers
    public function toggleUser($id)
    {
        if (in_array($id, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$id]);
        } else {
            $this->selectedUsers[] = $id;
        }
    }

    // Simpan relasi ke pivot
    public function save()
    {
        $department = Department::find($this->department_id);
        $department->users()->sync($this->selectedUsers);
        $this->dispatch(
            'alert',
            [
                'text' => 'Relasi user berhasil disimpan!',
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]
        );
    }

    public function render()
    {
        $this->updateSearchUser();
        return view('livewire.administration.relasi-dept-user.department-user-manager');
    }
}
