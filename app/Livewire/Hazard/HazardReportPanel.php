<?php

namespace App\Livewire\Hazard;

use App\Models\Hazard;
use Livewire\Component;
use App\Models\EventType;
use App\Models\Contractor;
use App\Models\Department;
use App\Enums\HazardStatus;
use App\Models\EventSubType;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class HazardReportPanel extends Component
{
    use WithPagination;
    public $filterStatus = ['submitted', 'in_progress', 'pending', 'closed'], $role;
    public $filterEventType;
    public $filterEventSubType;
    public $filterDepartment;
    public $filterContractor;
    public $openDropdownId = null;
    public $deptCont = 'department'; // default departemen
    public $search = '';
    public $searchContractor = '';
    public $showDropdown = false;
    public $showContractorDropdown = false;
    public $departments = [];
    public $contractors = [];
    public $department_id;
    public $contractor_id;
    public $action_due_date = '';
    public $start_date;
    public $end_date;
    // Properti ini yang akan mengontrol tampilan dropdown
    public bool $isDropdownOpen = false;


    public function toggleDropdownstatus()
    {
        $this->isDropdownOpen = !$this->isDropdownOpen;
    }
    public function updatedFilterStatus()
    {
        // Panggil logika filter data Anda di sini
        $this->filterData(); 
        
        // Opsional: Tutup dropdown setelah filter diterapkan
        $this->isDropdownOpen = false;
    }
    public function updatedDeptCont($value)
    {
        if ($value === 'department') {
            // Reset kontraktor jika pindah ke departemen
            $this->resetErrorBag(['contractor_id']);
            $this->reset(['contractor_id', 'searchContractor', 'contractors']);
        }
        if ($value === 'company') {
            // Reset departemen jika pindah ke kontraktor
            $this->resetErrorBag(['department_id']);
            $this->reset(['department_id', 'search', 'departments']);
        }
    }
    public function updatedActionDueDate($value)
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
    public function toggleDropdown($reportId)
    {

        $this->openDropdownId = $this->openDropdownId === $reportId ? null : $reportId;
    }
    public function updateStatus($reportId, $newStatus)
    {
        $report = Hazard::findOrFail($reportId);
        $userRole = Auth::user()->role;

        $valid = match ([$userRole, $report->status->value, $newStatus]) {
            // Moderator: kirim ke ERM
            ['moderator', 'submitted', 'in_progress'] => true,

            // ERM: kembalikan ke moderator
            ['erm', 'in_progress', 'pending'] => true,
            ['erm', 'in_progress', 'closed'] => true,

            // Moderator: tutup laporan
            ['moderator', 'pending', 'closed'] => true,

            // Moderator: kirim ulang ke ERM
            ['moderator', 'pending', 'in_progress'] => true,

            // Moderator: batalkan
            ['moderator', 'submitted', 'cancelled'],
            ['moderator', 'pending', 'cancelled'] => true,
            // Moderator: buka kembali report
            ['moderator', 'closed', 'in_progress'] => true,
            ['moderator', 'cancelled', 'submitted'] => true,
            ['moderator', 'cancelled', 'closed'] => true,
            default => false,
        };

        if (! $valid) {
            session()->flash('message', 'Aksi tidak diizinkan untuk status/role saat ini.');
            return;
        }
        // prevent non-moderator from reopening closed
        if ($report->status === HazardStatus::Closed && $userRole !== 'moderator') {
            abort(403, 'Hanya moderator yang dapat membuka kembali laporan yang sudah ditutup.');
        }

        $report->status = $newStatus;
        $report->save();

        // TODO: kirim notifikasi otomatis

        session()->flash('message', "Status laporan #{$report->id} diubah menjadi {$newStatus}.");
    }
    protected function filterModeratorReports($query)
    {
        $user = Auth::user();

        $assignedDept = $user->moderatorAssignments->pluck('department_id')->filter()->unique();
        $assignedContractors = $user->moderatorAssignments->pluck('contractor_id')->filter()->unique();
        $assignedCompanies = $user->moderatorAssignments->pluck('company_id')->filter()->unique();

        $companyDept = \App\Models\Department::whereIn('company_id', $assignedCompanies)->pluck('id');
        $companyContractor = \App\Models\Contractor::whereIn('company_id', $assignedCompanies)->pluck('id');

        $allDept = $assignedDept->merge($companyDept)->unique();
        $allContractors = $assignedContractors->merge($companyContractor)->unique();

        $query->where(function ($q) use ($allDept, $allContractors, $assignedCompanies) {
            $q->when($allDept->isNotEmpty(), fn($q) => $q->whereIn('department_id', $allDept))
                ->when($allContractors->isNotEmpty(), fn($q) => $q->orWhereIn('contractor_id', $allContractors))
                ->when($assignedCompanies->isNotEmpty(), fn($q) => $q->orWhereIn('company_id', $assignedCompanies));
        });
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->departments = Department::where('department_name', 'like', '%' . $this->search . '%')
                ->orderBy('department_name')
                ->limit(10)
                ->get();
            $this->showDropdown = true;
        } else {
            $this->departments = [];
            $this->showDropdown = false;
            $this->filterDepartment = '';
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor', 'contractor_id');
        $this->department_id = $id;
        $this->search = $name;
        $this->filterDepartment = $name;
        $this->showDropdown = false;
    }
    public function updatedSearchContractor()
    {
        if (strlen($this->searchContractor) > 1) {
            $this->contractors = Contractor::query()
                ->where('contractor_name', 'like', '%' . $this->searchContractor . '%')
                ->orderBy('contractor_name')
                ->limit(10)
                ->get();
            $this->showContractorDropdown = true;
        } else {
            $this->contractors = [];
            $this->showContractorDropdown = true;
            $this->filterContractor = '';
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('search', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->filterContractor = $name;
        $this->showContractorDropdown = false;
    }



    public function render()
    {
        $query = Hazard::with('pelapor')->withHazardCounts()->latest();

        // Tambahkan withCount untuk menghitung relasi


        // Terapkan scope untuk setiap filter
        $query->when($this->filterStatus !== 'all', function ($q) {
            $q->status($this->filterStatus);
        });

        $query->when($this->filterEventType, function ($q) {
            $q->byEventType($this->filterEventType);
        });

        $query->when($this->filterEventSubType, function ($q) {
            $q->byEventSubType($this->filterEventSubType);
        });

        $query->when($this->filterDepartment, function ($q) {
            $q->byDepartment($this->filterDepartment);
        });

        $query->when($this->filterContractor, function ($q) {
            $q->byContractor($this->filterContractor);
        });
        // ⚡️ Tambahkan filter rentang tanggal di sini
        $query->when($this->start_date && $this->end_date, function ($q) {
            $q->dateRange($this->start_date, $this->end_date);
        });
        $this->role = Auth::user()->role;

        if ($this->role === 'moderator') {
            $this->filterModeratorReports($query);
        }

        $reports = $query->paginate(30);
        $availableStatuses = ['submitted', 'in_progress', 'pending', 'closed'];
        return view('livewire.hazard.hazard-report-panel', [
            'eventTypes' => EventType::where('event_type_name', 'like', '%' . 'hazard' . '%')->get(),
            'subTypes' => EventSubType::where('event_type_id', $this->filterEventType)->get(),
            'availableStatuses' => $availableStatuses,
            'reports' => $reports
        ]);
    }
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
