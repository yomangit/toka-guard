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
use App\Helpers\FileHelper;
use App\Models\ActionHazard;
use App\Models\EventSubType;
use Livewire\WithFileUploads;
use App\Models\RiskAssessment;
use App\Models\RiskMatrixCell;
use App\Models\RiskConsequence;
use App\Models\UnsafeCondition;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use App\Models\RiskAssessmentMatrix;
use Illuminate\Support\Facades\Auth;
use App\Helpers\DateBeforeOrEqualToday;
use App\Notifications\HazardReportNotif;
use Illuminate\Support\Collection;

class HazardForm extends Component
{
    use WithFileUploads;
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
    public $status;
    public $RiskAssessment;
    #[Validate('required')]
    public $likelihood_id;
    #[Validate('required')]
    public $consequence_id;
    #[Validate('required')]
    public $location_id;
    #[Validate]
    public $pelapor_id;
    #[Validate('required|string')]
    public $description;
    #[Validate('required|string')]
    public $immediate_corrective_action;
    #[Validate('required_without:contractor_id')]
    public $department_id;
    #[Validate('required_without:department_id')]
    public $contractor_id;
    #[Validate('required|string')]
    public $penanggungJawab;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf')]
    public $doc_deskripsi;
    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf')]
    public $doc_corrective;
    #[Validate('required|string')]
    public $tipe_bahaya;
    #[Validate('required|string')]
    public $sub_tipe_bahaya;
    #[Validate('required|string')]
    public $location_specific;

    #[Validate('required')]
    public $keyWord = 'kta';
    #[Validate('required_without:tindakan_tidak_aman')]
    public $kondisi_tidak_aman;
    #[Validate('required_without:kondisi_tidak_aman')]
    public $tindakan_tidak_aman;
    #[Validate(['required', 'date', new DateBeforeOrEqualToday])]
    public $tanggal;
    public $manualPelaporMode = false;
    public $manualPelaporName = '';
    public $manualActPelaporMode = false;
    public $manualActPelaporName = '';
    // input action
    public $actions = []; // kumpulan action sebelum disimpan
    public $action_description;
    public $action_due_date;
    public $actual_close_date;
    public $action_responsible_id;
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
        'description.string'       => 'Deskripsi harus berupa teks.',

        'immediate_corrective_action.required'     => 'Tindakan perbaikan langsung wajib diisi.',
        'immediate_corrective_action.string'       => 'Tindakan perbaikan langsung harus berupa teks.',

        'department_id.required_without' => 'Departemen wajib dipilih jika kontraktor tidak diisi.',
        'contractor_id.required_without' => 'Kontraktor wajib dipilih jika departemen tidak diisi.',

        'kondisi_tidak_aman.required_without' => 'Kondisi Tidak Aman wajib dipilih jika Tindakan Tidak Aman tidak diisi.',
        'tindakan_tidak_aman.required_without' => 'Tindakan Tidak Aman wajib dipilih jika Kondisi Tidak Aman tidak diisi.',

        'pelapor_id.required' => 'Pelapor wajib dipilih.',
        'penanggungJawab.required' => 'Penanggung jawab area wajib dipilih.',
        'penanggungJawab.string'   => 'Penanggung jawab harus berupa teks.',

        'tipe_bahaya.required'     => 'Tipe Bahaya wajib dipilih.',
        'tipe_bahaya.string'       => 'Tipe Bahaya harus berupa teks.',

        'sub_tipe_bahaya.required' => 'Sub Tipe Bahaya wajib dipilih.',
        'sub_tipe_bahaya.string'   => 'Sub Tipe Bahaya harus berupa teks.',

        'tanggal.required'         => 'Tanggal wajib dipilih.',
        'tanggal.date'             => 'Tanggal harus berupa format tanggal valid.',
        'doc_deskripsi.file'   => 'File deskripsi harus berupa berkas yang valid.',
        'doc_deskripsi.mimes'  => 'File deskripsi hanya boleh dalam format JPG, JPEG, PNG, atau PDF.',
        'doc_deskripsi.max'    => 'Ukuran file deskripsi maksimal 2 MB.',

        'doc_corrective.file'  => 'File tindakan perbaikan harus berupa berkas yang valid.',
        'doc_corrective.mimes' => 'File tindakan perbaikan hanya boleh dalam format JPG, JPEG, PNG, atau PDF.',
        'doc_corrective.max'   => 'Ukuran file tindakan perbaikan maksimal 2 MB.',
    ];
    public function mount()
    {
        if (Auth::check()) {
            $this->pelapor_id = Auth::id();
            $this->searchPelapor = Auth::user()->name;
        }
        $this->likelihoods = Likelihood::orderByDesc('level')->get();
        $this->consequences = RiskConsequence::orderBy('level')->get();
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
        $this->manualPelaporMode = false;
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
        $this->pelapor_id = null;
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
    public function updatedManualActPelaporName($value)
    {
        $this->action_responsible_id = null;
    }

    public function addActPelaporManual()
    {
        $this->searchActResponsibility = $this->manualActPelaporName;
        $this->showActPelaporDropdown = false;
        $this->action_responsible_id = null;
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
    public function addAction()
    {
        $this->validate([
            'action_description' => 'required|string',
            'action_due_date' => 'required|date',
            'actual_close_date' => 'required|date',
            'action_responsible_id' => 'required|exists:users,id',
        ]);
        $this->actions[] = [
            'description' => $this->action_description,
            'due_date' => $this->action_due_date,
            'actual_close_date' => $this->actual_close_date,
            'responsible_id' => $this->action_responsible_id,
        ];
        $this->dispatch('alert', [
            'text' => "Tindakan Lanjutan berhasil dibuat!!",
            'duration' => 5000,
            'destination' => '/contact',
            'newWindow' => true,
            'close' => true,
            'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
        ]);
        // reset input sementara
        $this->reset(['action_description', 'action_due_date', 'action_responsible_id', 'searchActResponsibility']);
        $this->dispatch('reset-ckeditor');
    }

    public function removeAction($index)
    {
        unset($this->actions[$index]);
        $this->actions = array_values($this->actions); // reindex
        $this->dispatch(
            'alert',
            [
                'text' => "Tindakan Lanjutan berhasil di hapus!!!",
                'duration' => 5000,
                'destination' => '/contact',
                'newWindow' => true,
                'close' => true,
                'backgroundColor' => "linear-gradient(to right, #ff3333, #ff6666)",
            ]
        );
    }
    public function submit()
    {
        $this->validate();
        DB::transaction(function () {
            $lastReport = Hazard::latest('id')->first();
            $nextId = $lastReport ? $lastReport->id + 1 : 1;
            $referenceNumber = 'LH-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            $docDeskripsiPath = null;
            $docCorrectivePath = null;

            $tanggal_time = Carbon::createFromFormat('d-m-Y H:i', $this->tanggal)->format('Y-m-d H:i:s');
            $tanggal = Carbon::createFromFormat('d-m-Y H:i', $this->tanggal)->format('Y-m-d');


            if ($this->doc_deskripsi) {
                $docDeskripsiPath = FileHelper::compressAndStore($this->doc_deskripsi, 'sebelum_perbaikan');
            }
            if ($this->doc_corrective) {
                $docCorrectivePath = FileHelper::compressAndStore($this->doc_corrective, 'sesudah_perbaikan');
            }

            $riskLevel = null;
            if ($this->consequence_id && $this->likelihood_id) {
                $riskLevel = RiskMatrixCell::where('likelihood_id', $this->likelihood_id)
                    ->where('risk_consequence_id', $this->consequence_id)
                    ->value('severity');
            }

            $pelaporId = $this->pelapor_id ?: null;

            // 1. Simpan hazard
            $hazard = Hazard::create([
                'no_referensi'           => $referenceNumber,
                'event_type_id'          => $this->tipe_bahaya,
                'event_sub_type_id'      => $this->sub_tipe_bahaya,
                'department_id'          => $this->department_id,
                'contractor_id'          => $this->contractor_id,
                'pelapor_id'             => $pelaporId,
                'penanggung_jawab_id'    => $this->penanggungJawab,
                'location_id'            => $this->location_id,
                'location_specific'      => $this->location_specific,
                'tanggal'                => $tanggal_time,
                'description'            => $this->description,
                'doc_deskripsi'          => $docDeskripsiPath,
                'immediate_corrective_action' => $this->immediate_corrective_action,
                'doc_corrective'         => $docCorrectivePath,
                'key_word'               => $this->keyWord,
                'kondisi_tidak_aman_id'  => $this->kondisi_tidak_aman,
                'tindakan_tidak_aman_id' => $this->tindakan_tidak_aman,
                'consequence_id'         => $this->consequence_id,
                'likelihood_id'          => $this->likelihood_id,
                'risk_level'             => $riskLevel,
                'manualPelaporName'      => $this->pelapor_id ? User::find($this->pelapor_id)?->name : $this->manualPelaporName,
            ]);

            // 2. Simpan semua action
            foreach ($this->actions as $act) {
                $due_date = Carbon::createFromFormat('d-m-Y', $act['due_date'])->format('Y-m-d');
                $actual_close_date = Carbon::createFromFormat('d-m-Y', $act['actual_close_date'])->format('Y-m-d');
                ActionHazard::create([
                    'hazard_id'     => $hazard->id,
                    'original_date'     => $tanggal,
                    'description'   => $act['description'],
                    'due_date'      => $due_date,
                    'actual_close_date'      => $actual_close_date,
                    'responsible_id' => $act['responsible_id'],
                ]);
            }

            // 3. Notifikasi
            // Dapatkan Penanggung Jawab dari relasi
            $penanggungJawab = $hazard->penanggungJawab;
            if ($penanggungJawab) {
                $penanggungJawab->notify(new HazardReportNotif($hazard));
            }
        });

        // 4. Feedback ke user
        $this->dispatch('alert', [
            'text' => "Laporan berhasil dikirim!",
            'duration' => 5000,
            'destination' => '/contact',
            'newWindow' => true,
            'close' => true,
            'backgroundColor' => "background: linear-gradient(135deg, #00c853, #00bfa5);",
        ]);

        $this->resetForm();
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

    public function render()
    {
        return view('livewire.hazard.hazard-form', [
            'users' => User::limit(10)->get(),
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
    public function resetForm()
    {
        $this->reset([
            'tipe_bahaya',
            'sub_tipe_bahaya',
            'status',
            'department_id',
            'contractor_id',
            'pelapor_id',
            'penanggungJawab',
            'location_id',
            'location_specific',
            'tanggal',
            'description',
            'doc_deskripsi',
            'immediate_corrective_action',
            'doc_corrective',
            'keyWord',
            'kondisi_tidak_aman',
            'tindakan_tidak_aman',
            'consequence_id',
            'likelihood_id',
            'actions',  // <--- penting
        ]);
    }
}
