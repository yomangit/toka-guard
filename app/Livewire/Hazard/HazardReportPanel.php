<?php

namespace App\Livewire\Hazard;

use App\Models\Hazard;
use Livewire\Component;
use App\Enums\HazardStatus;
use Illuminate\Support\Facades\Auth;

class HazardReportPanel extends Component
{
    public $filterStatus = 'all', $role;
    public $openDropdownId = null;

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
    public function render()
    {
        $query = Hazard::with('pelapor')->latest();
        $this->role = Auth::user()->role;
        if ($this->role === 'moderator') {
            $this->filterModeratorReports($query);
        }
        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }
        $reports = $query->get();
        return view('livewire.hazard.hazard-report-panel', compact('reports'));
    }
    public static function modalMaxWidth(): string
    {
        return 'sm';
    }
}
