<?php

namespace App\Livewire\Hazard;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Hazard;
use Livewire\Component;
use App\Models\Location;
use App\Models\EventType;
use App\Models\UnsafeAct;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\Likelihood;
use App\Enums\HazardStatus;
use App\Helpers\FileHelper;
use Livewire\Attributes\On;
use App\Models\ActionHazard;
use App\Models\EventSubType;
use App\Models\ErmAssignment;
use Livewire\WithFileUploads;
use App\Models\HazardWorkflow;
use App\Models\RiskAssessment;
use App\Models\RiskMatrixCell;
use App\Models\RiskConsequence;
use App\Models\UnsafeCondition;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\RiskAssessmentMatrix;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\HazardSubmittedNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HazardDetail extends Component
{
    use WithFileUploads, AuthorizesRequests;
    public $hazard ;
    public string $proceedTo = '';
    public array $availableTransitions = [];
    public string $effectiveRole = '';
    public ?int $assignTo1 = null;
    public ?int $assignTo2 = null;
    public array $ermList = [];
    public $asModerator = false;
    public $asErm = false;

    // Data dari Form
    // field tambahan tanpa aturan validasi
    public $deptCont = 'department'; // default departemen
    public $search = '';
    public $searchLocation = '';
    public $searchPelapor = '';
    public $searchActResponsibility = '';
    public $locations = [];
    public $pelapors = [];
    public $pelaporsAct = [];
    public $departments = [];
    public $showDropdown = false;
    public $showLocationDropdown = false;
    public $showPelaporDropdown = false;
    public $showActPelaporDropdown = false;
    public $searchContractor = '';
    public $contractors = [];
    public $showContractorDropdown = false;
    public $penanggungJawabOptions = [];
    public $likelihoods, $consequences;
    public $selectedLikelihoodId = null;
    public $selectedConsequenceId = null;
    public $RiskAssessment;
    public $status;
    #[Validate('required')]
    public $likelihood_id;
    #[Validate('required')]
    public $consequence_id;
    #[Validate('required')]
    public $location_id;
    #[Validate]
    public $pelapor_id;
    #[Validate('required')]
    public $description;
    #[Validate('required')]
    public $immediate_corrective_action;
    #[Validate('required_without:contractor_id')]
    public $department_id;
    #[Validate('required_without:department_id')]
    public $contractor_id;
    #[Validate('required')]
    public $penanggungJawab;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf')]
    public $new_doc_deskripsi;
    public $doc_deskripsi;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf')]
    public $new_doc_corrective;
    public $doc_corrective;
    #[Validate('required')]
    public $tipe_bahaya;
    #[Validate('required')]
    public $sub_tipe_bahaya;
    #[Validate('required')]
    public $location_specific;

    #[Validate('required')]
    public $keyWord = 'kta';
    #[Validate('required_without:tindakan_tidak_aman')]
    public $kondisi_tidak_aman;
    #[Validate('required_without:kondisi_tidak_aman')]
    public $tindakan_tidak_aman;
    #[Validate('required|date')]
    public $tanggal;
    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $manualActPelaporMode = false;
    public $manualActPelaporName = '';
    public $hazard_id;

    // Data Action Hazard
    public $action_description;
    public $action_due_date;
    public $action_actual_close_date;
    public $action_responsible_id;

    public $manualActPelaporModeEdit = false;
    public $showActPelaporDropdownEdit = false;
    public $pelaporsActEdit = [];
    public $searchActResponsibilityEdit = '';
    public $manualActPelaporNameEdit = '';

    public $edit_action_id;
    public $edit_action_description;
    public $edit_action_due_date;
    public $edit_action_actual_close_date;
    public $edit_action_responsible_id;
    public $edit_searchResponsibility;

    // Untuk menampilkan daftar ActionHazard terkait hazard
    public $actionHazards = [];

    public function rules()
    {
        return [
            'pelapor_id' => $this->manualPelaporMode ? 'nullable' : 'required',
            'manualPelaporName' => $this->manualPelaporMode ? 'required|string|max:255' : 'nullable',
        ];
    }
    protected $messages = [
        'likelihood_id.required'     => 'likelihood wajib diisi.',
        'consequence_id.required'     => 'consequence wajib diisi.',
        'location_id.required'     => 'Lokasi wajib diisi.',
        'location_specific.required'     => 'Lokasi Spesifik wajib diisi.',
        'description.required'     => 'Deskripsi wajib diisi.',
        'immediate_corrective_action.required'     => 'Tindakan perbaikan langsung wajib diisi.',
        'department_id.required_without' => 'Departemen wajib dipilih jika kontraktor tidak diisi.',
        'contractor_id.required_without' => 'Kontraktor wajib dipilih jika departemen tidak diisi.',
        'kondisi_tidak_aman.required_without' => 'Kondisi Tidak Aman wajib dipilih jika Tindakan Tidak Aman tidak diisi.',
        'tindakan_tidak_aman.required_without' => 'Tindakan Tidak Aman wajib dipilih jika Kondisi Tidak Aman tidak diisi.',
        'pelapor_id.required' => 'Pelapor wajib dipilih.',
        'penanggungJawab.required' => 'Penanggung jawab area wajib dipilih.',
        'tipe_bahaya.required'     => 'Tipe Bahaya wajib dipilih.',
        'sub_tipe_bahaya.required' => 'Sub Tipe Bahaya wajib dipilih.',
        'tanggal.required'         => 'Tanggal wajib dipilih.',
        'tanggal.date'             => 'Tanggal harus berupa format tanggal valid.',
        'new_doc_deskripsi.file'    => 'File deskripsi harus berupa berkas.',
        'new_doc_deskripsi.mimes'   => 'File deskripsi hanya boleh berupa JPG, JPEG, PNG, atau PDF.',
        'new_doc_deskripsi.max'     => 'Ukuran file deskripsi maksimal 2 MB.',

        'new_doc_corrective.file'   => 'File tindakan perbaikan harus berupa berkas.',
        'new_doc_corrective.mimes'  => 'File tindakan perbaikan hanya boleh berupa JPG, JPEG, PNG, atau PDF.',
        'new_doc_corrective.max'    => 'Ukuran file tindakan perbaikan maksimal 2 MB.',
    ];

    public function mount(Hazard $hazard)
    {
        
    
        $this->authorize('view', $hazard);
        $this->hazard = $hazard;
        $this->hazard_id = $hazard->id;
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();

        $this->tanggal = Carbon::createFromFormat('Y-m-d H:i:s', $this->hazard->tanggal)->format('d-m-Y H:i');
        $this->tipe_bahaya = $this->hazard->event_type_id;
        $this->sub_tipe_bahaya = $this->hazard->event_sub_type_id;
        $this->status = $this->hazard->status;
        $this->department_id = $this->hazard->department_id;
        $this->contractor_id = $this->hazard->contractor_id;
        $this->pelapor_id = $this->hazard->pelapor_id;
        $this->penanggungJawab = $this->hazard->penanggung_jawab_id;
        $this->location_id = $this->hazard->location_id;
        $this->location_specific = $this->hazard->location_specific;
        $this->description = $this->hazard->description;
        $this->doc_deskripsi = $this->hazard->doc_deskripsi;
        $this->immediate_corrective_action = $this->hazard->immediate_corrective_action;
        $this->doc_corrective = $this->hazard->doc_corrective;
        $this->keyWord = $this->hazard->key_word;
        $this->kondisi_tidak_aman = $this->hazard->kondisi_tidak_aman_id;
        $this->tindakan_tidak_aman = $this->hazard->tindakan_tidak_aman_id;
        $this->consequence_id = $this->hazard->consequence_id;
        $this->likelihood_id = $this->hazard->likelihood_id;
        // ✅ Load nama untuk ditampilkan di search input
        if ($this->pelapor_id) {
            // ✅ Jika pelapor_id ada → ambil nama user
            $this->searchPelapor = User::find($this->pelapor_id)?->name ?? '';
            $this->manualPelaporName = $this->searchPelapor; // biar konsisten juga
        } else {
            // ✅ Jika pelapor_id null → pakai manualPelaporName dari DB
            $this->manualPelaporName = $this->hazard->manualPelaporName ?? '';
            $this->searchPelapor     = $this->manualPelaporName;
            $this->manualPelaporMode = true; // langsung aktifkan mode input manual
        }
        if ($this->department_id) {
            $department = Department::with('users')->find($this->department_id);
            $this->search =  $department?->department_name ?? '';
            $this->penanggungJawabOptions = $department
                ? $department->users()->select('users.id', 'users.name')->get()->toArray()
                : [];
            $this->deptCont = 'department';
        }
        if ($this->contractor_id) {
            $contractor = Contractor::with('users')->find($this->contractor_id);
            $this->searchContractor = $contractor?->contractor_name ?? '';
            $this->penanggungJawabOptions = $contractor
                ? $contractor->users()->select('users.id', 'users.name')->get()->toArray()
                : [];
            $this->deptCont = 'company';
        }
        if ($this->location_id) {
            $this->searchLocation = Location::find($this->location_id)?->name ?? '';
        }

        if ($this->department_id) {
            $this->deptCont = 'department';
            $this->search = Department::find($this->department_id)?->department_name ?? '';
        }
        if ($this->contractor_id) {
            $this->deptCont = 'company';
            $this->searchContractor = Contractor::find($this->contractor_id)?->contractor_name ?? '';
        }
        $id_table = RiskMatrixCell::where('likelihood_id', $this->likelihood_id)->where('risk_consequence_id', $this->consequence_id)->first()->id;
        $risk_assessment_id = RiskAssessmentMatrix::where('risk_matrix_cell_id', $id_table)->first()->risk_assessment_id;
        $this->RiskAssessment = RiskAssessment::whereId($risk_assessment_id)->first();

        $this->loadActionHazards();
    }
    protected function setEffectiveRole(): void
    {
        $userId = Auth::id();
        $dept   = $this->hazard->department_id;
        $cont   = $this->hazard->contractor_id;
        $comp   = null;
        // Ambil dari relasi departemen
        if ($dept) {
            $comp = DB::table('departments')->where('id', $dept)->value('company_id');
        }
        // Kalau tidak ada di department, cek contractor
        if (!$comp && $cont) {
            $comp = DB::table('contractors')->where('id', $cont)->value('company_id');
        }
        // Cek apakah user ditugaskan sebagai ERM
        $isErm = DB::table('erm_assignments')
            ->where('user_id', $userId)
            ->where(function ($q) use ($dept, $cont) {
                if ($dept) {
                    $q->orWhere('department_id', $dept);
                }
                if ($cont) {
                    $q->orWhere('contractor_id', $cont);
                }
            })
            ->exists();
        // Cek apakah user ditugaskan sebagai Moderator
        $isMod = DB::table('moderator_assignments')
            ->where('user_id', $userId)
            ->where(function ($q) use ($dept, $cont, $comp) {
                if ($dept) {
                    $q->orWhere('department_id', $dept);
                }
                if ($cont) {
                    $q->orWhere('contractor_id', $cont);
                }
                if ($comp) {
                    $q->orWhere('company_id', $comp);
                }
            })
            ->exists();
        // --- Perubahan di sini ---
        $currentStatus = $this->hazard->status; // contoh: 'in_progress'
        // Kumpulkan semua role user
        $roles = [];
        if ($isMod) $roles[] = 'moderator';
        if ($isErm) $roles[] = 'erm';
        // Cek di tabel workflow: role mana yang boleh transisi dari current status
        $allowedRole = DB::table('hazard_workflows')
            ->whereIn('role', $roles)
            ->where('from_status', $currentStatus)
            ->pluck('role')
            ->first();
        // Set effectiveRole
        $this->effectiveRole = $allowedRole ?? '';
        $this->asModerator   = $this->effectiveRole === 'moderator';
        $this->asErm         = $this->effectiveRole === 'erm';
    }
    protected function loadAvailableTransitions(): void
    {
        $this->availableTransitions = HazardWorkflow::getAvailableTransitions(
            $this->hazard->status,
            $this->effectiveRole
        );
    }
    protected function loadErmList(): void
    {
        $dept = $this->hazard->department_id;
        $cont = $this->hazard->contractor_id;
        $userIds = DB::table('erm_assignments')
            ->select('user_id')
            ->when($dept || $cont, function ($q) use ($dept, $cont) {
                $q->where(function ($q2) use ($dept, $cont) {
                    if ($dept) {
                        $q2->orWhere('department_id', $dept);
                    }
                    if ($cont) {
                        $q2->orWhere('contractor_id', $cont);
                    }
                });
            })
            ->pluck('user_id')
            ->toArray();
        $this->ermList = User::whereIn('id', $userIds)->get()->toArray();
    }
    public function processAction()
    {
        $newStatus = $this->proceedTo;
        if (!HazardWorkflow::isValidTransition($this->hazard->status, $newStatus, $this->effectiveRole)) {
            session()->flash('message', 'Transisi tidak valid untuk peran dan status saat ini.');
            return;
        }
        if ($newStatus === 'in_progress') {
            $assignIds = array_filter([$this->assignTo1, $this->assignTo2]);
            $this->hazard->assignedErms()->sync($assignIds);
        }
        $this->hazard->status = $newStatus;
        $this->hazard->save();
        $this->loadAvailableTransitions();
        $isDisabled = in_array($newStatus, ['cancelled', 'closed']);
        $this->dispatch('hazardStatusChanged', ['isDisabled' => $isDisabled]);
        $this->dispatch(
            'alert',
            [
                'text' => "Status Berhasil di Perbaharui!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => false,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
        $this->reset('proceedTo');
    }
    public function uploadImage()
    {
        if (request()->hasFile('upload')) {
            $file     = request()->file('upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path     = $file->storeAs('uploads/ckeditor', $filename, 'public');

            return response()->json([
                'url' => asset('storage/' . $path),
            ]);
        }
    }
    public function updated($propertyName)
    {
        $fieldsToValidate = [
            'location_id',
            'location_specific',
            'description',
            'severity',
            'department_id',
            'contractor_id',
            'penanggungJawab',
            'tanggal',
        ];
        if (in_array($propertyName, $fieldsToValidate)) {
            $this->validateOnly($propertyName);
        }
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
    public function updatedKeyWord($value)
    {
        if ($value === 'kta') {
            $this->resetErrorBag(['tindakan_tidak_aman']);
            $this->reset(['tindakan_tidak_aman']);
        } elseif ($value === 'tta') {
            $this->resetErrorBag(['kondisi_tidak_aman']);
            $this->reset(['kondisi_tidak_aman']);
        }
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
        }
    }
    public function selectDepartment($id, $name)
    {
        $this->reset('searchContractor', 'contractor_id');
        $this->department_id = $id;
        $this->search = $name;
        $this->showDropdown = false;

        // Ambil user relasi dari departemen
        $department = Department::with('users')->find($id);
        $this->penanggungJawabOptions = $department
            ? $department->users()->select('users.id', 'users.name')->get()->toArray()
            : [];

        $this->validateOnly('department_id');
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
        }
    }
    public function selectContractor($id, $name)
    {
        $this->reset('search', 'department_id');
        $this->contractor_id = $id;
        $this->searchContractor = $name;
        $this->showContractorDropdown = false;
        // Ambil user relasi dari contractor
        $contractor = Contractor::with('users')->find($id);
        $this->penanggungJawabOptions = $contractor
            ? $contractor->users()->select('users.id', 'users.name')->get()->toArray()
            : [];
        $this->validateOnly('contractor_id');
    }
    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 1) {
            $this->locations = Location::where('name', 'like', '%' . $this->searchLocation . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showLocationDropdown = true;
        } else {
            $this->locations = [];
            $this->showLocationDropdown = false;
        }
    }
    public function selectLocation($id, $name)
    {
        $this->location_id = $id;
        $this->searchLocation = $name;
        $this->showLocationDropdown = false;
        $this->validateOnly('location_id');
    }
    public function updatedSearchPelapor()
    {
        $this->reset('manualPelaporName');
        $this->manualPelaporMode = false;
        if (strlen($this->searchPelapor) > 1) {
            $this->pelapors = User::where('name', 'like', '%' . $this->searchPelapor . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showPelaporDropdown = true;
        } else {
            $this->pelapors = [];
            $this->showPelaporDropdown = false;
        }
    }
    public function selectPelapor($id, $name)
    {
        $this->pelapor_id = $id;
        $this->searchPelapor = $name;
        $this->showPelaporDropdown = false;
        $this->validateOnly('pelapor_id');
    }
    public function enableManualPelapor()
    {
        $this->manualPelaporMode = true;
        $this->manualPelaporName = $this->searchPelapor; // isi default sama dengan isi search
    }
    public function updatedManualPelaporName($value)
    {
        $this->pelapor_id = null;
    }
    public function addPelaporManual()
    {
        $this->searchPelapor = $this->manualPelaporName;
        $this->showPelaporDropdown = false;
    }
    public function updatedSearchActResponsibility()
    {
        $this->reset('manualActPelaporName');
        $this->manualActPelaporMode = false;
        if (strlen($this->searchActResponsibility) > 1) {
            $this->pelaporsAct = User::where('name', 'like', '%' . $this->searchActResponsibility . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showActPelaporDropdown = true;
        } else {
            $this->pelaporsAct = [];
            $this->showActPelaporDropdown = false;
        }
    }
    public function selectActPelapor($id, $name)
    {
        $this->action_responsible_id = $id;
        $this->searchActResponsibility = $name;
        $this->showActPelaporDropdown = false;
        $this->manualActPelaporMode = false;
        $this->validateOnly('action_responsible_id');
    }
    public function enableManualActPelapor()
    {
        $this->manualActPelaporMode = true;
        $this->manualActPelaporName = $this->searchPelapor; // isi default sama dengan isi search
    }
    public function getIsFormValidProperty()
    {
        // Kalau user pilih department_id atau contractor_id salah satu boleh
        $validCompanyDept = $this->department_id || $this->contractor_id;
        return $validCompanyDept
            && !empty($this->location)
            && !empty($this->description)
            && !empty($this->severity);
    }

    public function submit()
    {
        $this->validate();
        $docDeskripsiPath = null;
        $docCorrectivePath = null;
        // Convert tanggal ke format Y-m-d H:i:s
        $tanggal = Carbon::createFromFormat('d-m-Y H:i', $this->tanggal)->format('Y-m-d H:i:s');
        // Update file sebelum perbaikan
        if ($this->new_doc_deskripsi) {
            // Hapus file lama (opsional)
            if ($this->doc_deskripsi && Storage::disk('public')->exists($this->doc_deskripsi)) {
                Storage::disk('public')->delete($this->doc_deskripsi);
            }

            // Simpan file baru
            $docDeskripsiPath = FileHelper::compressAndStore($this->new_doc_deskripsi, 'sebelum_perbaikan');
            $this->doc_deskripsi = $docDeskripsiPath;
        }

        // Update file sesudah perbaikan
        if ($this->new_doc_corrective) {
            if ($this->doc_corrective && Storage::disk('public')->exists($this->doc_corrective)) {
                Storage::disk('public')->delete($this->doc_corrective);
            }

            $docCorrectivePath = FileHelper::compressAndStore($this->new_doc_corrective, 'sesudah_perbaikan');
            $this->doc_corrective = $docCorrectivePath;
        }
        // Hitung risk level (opsional: ambil dari RiskMatrixCell atau kalkulasi manual)
        $riskLevel = null;
        if ($this->consequence_id && $this->likelihood_id) {
            $riskLevel = \App\Models\RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
                ->where('risk_consequence_id', $this->consequence_id)
                ->value('severity');
        }
        $updateData = [
            'event_type_id'          => $this->tipe_bahaya,
            'event_sub_type_id'      => $this->sub_tipe_bahaya,
            'status'                 => 'submitted',
            'department_id'          => $this->department_id,
            'contractor_id'          => $this->contractor_id,
            'pelapor_id'             => $this->pelapor_id,
            'penanggung_jawab_id'    => $this->penanggungJawab,
            'location_id'            => $this->location_id,
            'location_specific'      => $this->location_specific,
            'tanggal'                => $tanggal,
            'description'            => $this->description,
            'immediate_corrective_action' => $this->immediate_corrective_action,
            'key_word'               => $this->keyWord,
            'kondisi_tidak_aman_id'  => $this->kondisi_tidak_aman,
            'tindakan_tidak_aman_id' => $this->tindakan_tidak_aman,
            'consequence_id'         => $this->consequence_id,
            'likelihood_id'          => $this->likelihood_id,
            'risk_level'             => $riskLevel,
            'manualPelaporName' => $this->pelapor_id ? User::find($this->pelapor_id)?->name : $this->manualPelaporName,
        ];

        // Hanya update kalau ada file baru
        if ($docDeskripsiPath) {
            $updateData['doc_deskripsi'] = $docDeskripsiPath;
        }

        if ($docCorrectivePath) {
            $updateData['doc_corrective'] = $docCorrectivePath;
        }

        $hazard = Hazard::findOrFail($this->hazard->id);
        $hazard->update($updateData);

        $ermUsers = ErmAssignment::where('department_id', $this->department_id)
            ->orWhere('contractor_id', $this->contractor_id)
            ->with('user')
            ->get()
            ->pluck('user');
        $this->dispatch(
            'alert',
            [
                'text' => "Laporan berhasil diupdate!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
            ]
        );
    }
    public function edit($likelihoodId, $consequenceId)
    {
        $this->likelihood_id = $likelihoodId;
        $this->consequence_id = $consequenceId;

        $this->selectedLikelihoodId = $likelihoodId;
        $this->selectedConsequenceId = $consequenceId;

        $this->loadRiskAssessment();
    }

    public function updatedConsequenceId()
    {
        $this->loadRiskAssessment();
    }

    public function updatedLikelihoodId()
    {
        $this->loadRiskAssessment();
    }
    protected function loadRiskAssessment(): void
    {
        if (!$this->likelihood_id || !$this->consequence_id) {
            $this->RiskAssessment = null;
            return;
        }

        $cell = RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
            ->where('risk_consequence_id', $this->consequence_id)
            ->first();

        if (!$cell) {
            $this->RiskAssessment = null;
            return;
        }

        $matrix = RiskAssessmentMatrix::where('risk_matrix_cell_id', $cell->id)->first();

        $this->RiskAssessment = $matrix
            ? RiskAssessment::find($matrix->risk_assessment_id)
            : null;
    }


    public function addActionHazard()
    {
        $this->validate(
            [
                'action_description'       => 'required|string',
                'action_due_date'          => 'required|date',
                'action_actual_close_date' => 'nullable|date',
                'action_responsible_id'    => 'required|integer|exists:users,id',
            ],
            [
                'action_description.required'       => 'Deskripsi tindakan wajib diisi.',
                'action_description.string'         => 'Deskripsi tindakan harus berupa teks.',

                'action_due_date.required'          => 'Batas waktu penyelesaian wajib diisi.',
                'action_due_date.date'              => 'Batas waktu penyelesaian harus berupa tanggal yang valid.',

                'action_actual_close_date.date'     => 'Tanggal penyelesaian tindakan harus berupa tanggal yang valid.',

                'action_responsible_id.required'    => 'Penanggung jawab wajib dipilih.',
                'action_responsible_id.integer'     => 'Penanggung jawab tidak valid.',
                'action_responsible_id.exists'      => 'Penanggung jawab yang dipilih tidak ditemukan.',
            ]
        );


        ActionHazard::create([
            'hazard_id'        => $this->hazard->id,
            'orginal_date'     => now(), // atau bisa diambil dari form jika ada
            'description'      => $this->action_description,
            'due_date'         => Carbon::parse($this->action_due_date),
            'actual_close_date' => $this->action_actual_close_date
                ? Carbon::parse($this->action_actual_close_date)
                : null,
            'responsible_id'   => $this->action_responsible_id,
        ]);
        $this->dispatch(
            'alert',
            [
                'text' => "Action Hazard berhasil ditambahkan!",
                'duration' => 4000,
                'backgroundColor' => "background: linear-gradient(135deg, #42a5f5, #478ed1);",
            ]
        );
        // Refresh list setelah simpan
        $this->loadActionHazards();

        // Reset form
        $this->reset([
            'action_description',
            'action_due_date',
            'action_actual_close_date',
            'action_responsible_id',
            'searchActResponsibility',
        ]);
    }
    public function removeAction($id)
    {
        // Jika datanya hanya di array property
        $this->actionHazards = collect($this->actionHazards)
            ->reject(fn($act) => $act['id'] == $id)
            ->values()
            ->toArray();
        // Jika mau hapus di database juga:
        $action = ActionHazard::find($id);
        if ($action) {
            $action->delete(); // event deleted + activity log terpicu
            $this->dispatch(
                'alert',
                [
                    'text' => "Data berhasil di hapus!!!",
                    'duration' => 5000,
                    'destination' => '/contact',
                    'newWindow' => true,
                    'close' => true,
                    'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
                ]
            );
        }
    }
    public function loadEditAction($id)
    {
        $action = ActionHazard::findOrFail($id);
        $this->edit_action_id               = $action->id;
        $this->edit_action_description      = $action->description;
        $this->edit_action_due_date         = optional($action->due_date)->format('d-m-Y');
        $this->edit_action_actual_close_date = optional($action->actual_close_date)->format('d-m-Y');
        $this->edit_action_responsible_id   = $action->responsible_id;
        $this->searchActResponsibilityEdit   = optional(User::find($action->responsible_id))->name;
        // kirim event ke Alpine supaya modal dibuka setelah data siap
        $this->dispatch('open-edit-action');
    }

    public function updatedSearchActResponsibilityEdit()
    {
        $this->reset('manualActPelaporName');
        $this->manualActPelaporModeEdit = false;
        if (strlen($this->searchActResponsibilityEdit) > 1) {
            $this->pelaporsActEdit = User::where('name', 'like', '%' . $this->searchActResponsibilityEdit . '%')
                ->orderBy('name')
                ->limit(10)
                ->get();
            $this->showActPelaporDropdownEdit = true;
        } else {
            $this->pelaporsActEdit = [];
            $this->showActPelaporDropdownEdit = false;
        }
    }
    public function selectActPelaporEdit($id, $name)
    {
        $this->edit_action_responsible_id = $id;
        $this->searchActResponsibilityEdit = $name;
        $this->showActPelaporDropdownEdit = false;
        $this->manualActPelaporModeEdit = false;
        $this->validateOnly('edit_action_responsible_id');
    }
    public function addActPelaporManualEdit()
    {
        $this->searchActResponsibilityEdit = $this->manualActPelaporNameEdit;
        $this->showActPelaporDropdownEdit = false;
    }
    public function enableManualActPelaporEdit()
    {
        $this->manualActPelaporModeEdit = true;
        $this->manualActPelaporNameEdit = $this->searchActResponsibilityEdit; // isi default sama dengan isi search
    }

    public function updateAction()
    {
        $this->validate(
            [
                'edit_action_description'       => 'required|string',
                'edit_action_due_date'          => 'required|date_format:d-m-Y',
                'edit_action_actual_close_date'  => 'nullable|date_format:d-m-Y',
                'edit_action_responsible_id'    => 'required|integer',
            ],
            [
                'edit_action_description.required'      => 'Deskripsi tindakan wajib diisi.',
                'edit_action_description.string'        => 'Deskripsi tindakan harus berupa teks.',

                'edit_action_due_date.required'         => 'Tanggal batas waktu wajib diisi.',
                'edit_action_due_date.date_format'      => 'Tanggal batas waktu harus dalam format dd-mm-YYYY.',

                'edit_action_actual_close_date.date_format' => 'Tanggal penyelesaian harus dalam format dd-mm-YYYY.',

                'edit_action_responsible_id.required'   => 'Penanggung jawab wajib dipilih.',
                'edit_action_responsible_id.integer'    => 'Penanggung jawab tidak valid.',
            ]
        );

        $action = ActionHazard::findOrFail($this->edit_action_id);
        $action->update([
            'description'       => $this->edit_action_description,
            'due_date'          => Carbon::createFromFormat('d-m-Y', $this->edit_action_due_date),
            'actual_close_date' => $this->edit_action_actual_close_date ? Carbon::createFromFormat('d-m-Y', $this->edit_action_actual_close_date) : null,
            'responsible_id'    => $this->edit_action_responsible_id,
        ]);
        $this->dispatch(
            'alert',
            [
                'text' => "Action Hazard berhasil diupdate!",
                'duration' => 4000,
                'backgroundColor' => "background: linear-gradient(135deg, #42a5f5, #478ed1);",
            ]
        );
        $this->dispatch('close-modal', id: 'editActionModal');
        // Refresh list
        $this->loadActionHazards();
    }
    public function loadActionHazards()
    {
        $this->actionHazards = ActionHazard::with('responsible')->where('hazard_id', $this->hazard->id)->orderByDesc('created_at')->get()->toArray();
    }
    public function render()
    {
        $this->setEffectiveRole();
        $this->loadAvailableTransitions();
        $this->loadErmList();
        return view('livewire.hazard.hazard-detail', [
            'report' => Hazard::with('activities.causer')->findOrFail($this->hazard_id),
            'Department'   => Department::all(),
            'likelihoodss' => Likelihood::orderByDesc('level')->get(),
            'consequencess' => RiskConsequence::orderBy('level')->get(),
            'Contractors'  => Contractor::all(),
            'ktas' => UnsafeCondition::latest()->get(),
            'ttas' => UnsafeAct::latest()->get(),
            'eventTypes' => EventType::where('event_type_name', 'like', '%' . 'hazard' . '%')->get(),
            'subTypes' => EventSubType::where('event_type_id', $this->tipe_bahaya)->get()
        ]);
    }
}
