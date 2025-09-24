<?php

namespace App\Livewire\Manhours;

use Carbon\Carbon;
use App\Mail\TestEmail;
use App\Models\Company;
use App\Models\Manhour;
use Livewire\Component;
use App\Models\Custodian;
use App\Models\Contractor;
use App\Models\Department;
use App\Models\BusinessUnit;
use Livewire\WithPagination;
use App\Models\Department_group;
use App\Services\GraphMailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class Index extends Component
{
    use WithPagination;

    public $modalOpen;
    public $custodian = [];
    public $deptGroup = [];
    public $companies = [];
    public $selectedId = null;
    public $confirmingDelete = false;
    public $canCreate = false;
    // input umum
    public $date;
    public $entity_type;
    public $company;
    public $department;
    public $dept_group;
    public $form;



    // jobclass setup
    public $jobclasses = [
        'supervisor'     => 'Supervisor',
        'operational'    => 'Operational',
        'administration' => 'Administration',
    ];

    public $hide = [
        'supervisor'     => false,
        'operational'    => false,
        'administration' => false,
    ];

    public $manhours = [
        'supervisor'     => null,
        'operational'    => null,
        'administration' => null,
    ];

    public $manpower = [
        'supervisor'     => null,
        'operational'    => null,
        'administration' => null,
    ];

    protected function rules()
    {
        $rules = [
            'date'        => 'required',
            'entity_type' => 'required|string',
            'company'     => 'required|string',
            'department'  => 'required|string',
        ];

        foreach ($this->jobclasses as $key => $label) {
            $rules["manhours.$key"] = $this->hide[$key] ? 'nullable' : 'required|numeric|min:0';
            $rules["manpower.$key"] = $this->hide[$key] ? 'nullable' : 'required|numeric|min:0';
        }

        return $rules;
    }
    public function mount()
    {
        // kalau admin, ambil semua
        if (auth()->user()->roles()->where('role_id', 1)->exists() && $this->entity_type = 'owner') {
            $this->companies = [
                'owners' => BusinessUnit::all(),
                'contractors' => Contractor::all(),
            ];
        }
        elseif($this->entity_type = 'owner'){
             $this->companies = [
                'owners' => BusinessUnit::all(),
                'contractors' => collect([]),
            ];
        }
        else {
            // kalau contractor, ambil hanya contractor yg punya relasi
            $this->entity_type = 'contractor';
            $this->companies = [
                'owners' => collect([]), // kosong
                'contractors' => auth()->user()->contractors, // relasi dari user
            ];
        }
    }

    public function open_modal($id = null)
    {
        Gate::authorize('create', Manhour::class);
        $this->modalOpen = 'modal-open';
        $this->form = empty($id) ? 'Input' : 'Update';
        if ($id) {
            $this->selectedId = $id;
            $data = Manhour::findOrFail($id);

            $this->date        = Carbon::parse($data->date)->format('M-Y');
            $this->entity_type = strtolower($data->company_category) === "contractor" ? "contractor" : "owner";
            $this->company     = $data->company;
            $this->department  = $data->department;
            $this->dept_group  = $data->dept_group;
            $this->updatedCompany();
            $this->updatedDepartment();
            // reset dulu
            foreach ($this->jobclasses as $key => $label) {
                $this->hide[$key]     = true;
                $this->manhours[$key] = null;
                $this->manpower[$key] = null;
            }

            // isi sesuai data yg ada di DB
            $manhoursData = Manhour::where('date', $data->date)
                ->where('company', $data->company)
                ->where('department', $data->department)
                ->where('dept_group', $data->dept_group)
                ->get();

            foreach ($manhoursData as $row) {
                $key = array_search($row->job_class, $this->jobclasses);
                if ($key !== false) {
                    $this->hide[$key]     = false;
                    $this->manhours[$key] = $row->manhours;
                    $this->manpower[$key] = $row->manpower;
                }
            }
        }
    }
    public function close_modal()
    {
        // Tutup modal
        $this->reset('modalOpen', 'selectedId');

        // Reset semua input form ke default
        $this->reset([
            'date',
            'entity_type',
            'company',
            'department',
            'dept_group',
            'manhours',
            'manpower',
            'hide',
        ]);

        // Kalau perlu reset array jobclass manual
        foreach ($this->jobclasses as $key => $label) {
            $this->hide[$key]     = true;
            $this->manhours[$key] = null;
            $this->manpower[$key] = null;
        }
    }
    public string $recipient = '';
    public function updatedCompany()
    {
        if ($this->entity_type === "contractor") {
            $custodian = Contractor::where('contractor_name', 'LIKE', $this->company)->first()->id ?? null;
            $this->custodian = Custodian::where('contractor_id', $custodian)->get();
        } elseif ($this->entity_type === "owner") {
            $this->deptGroup = Department_group::get();
        } else {
            $this->reset('department');
        }
    }

    public function updatedDepartment()
    {
        $dept_id = Department::where('department_name', 'LIKE', $this->department)->value('id') ?? null;
        $this->dept_group = Department_group::where('department_id', $dept_id)->first()->Group->group_name ?? null;
    }
    public function updatedEntityType($value)
    {
        $user = Auth::user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            // 🔹 Admin: bisa lihat semua
            if ($value === 'owner') {
                $this->companies = BusinessUnit::pluck('company_name')->toArray();
            } elseif ($value === 'contractor') {
                $this->companies = Contractor::pluck('contractor_name')->toArray();
            }
        } else {
            // 🔹 Contractor user: hanya kontraktor miliknya
            if ($value === 'contractor') {
                $this->companies = $user->contractors()->pluck('contractor_name')->toArray();
            } else {
                $this->companies = []; // contractor user nggak boleh pilih owner
            }
        }

        // reset pilihan company setiap ganti entity_type
        $this->company = '';
    }

    public function render()
    {
        // authorize viewAny
        Gate::authorize('viewAny', Manhour::class);
        $user = auth()->user();

        if ($user->roles()->where('role_id', 1)->exists()) {
            $query = Manhour::query();
        } else {
            $contractorNames = $user->contractors()->pluck('contractor_name');
            $query = Manhour::whereIn('company', $contractorNames);
        }
        return view('livewire.manhours.index', [
            'bu'        => BusinessUnit::all(),
            'cont'      => Contractor::all(),
            'departemen' => Department::get(),
            'data_manhours'  => $query->latest()->paginate(10),
        ]);
    }

    private function saveManhours($mode = 'create', $id = null)
    {
        $this->validate();

        $company_category = $this->entity_type === "contractor"
            ? 'Contractor'
            : 'PT. Archi Indonesia';

        $bulan = Carbon::createFromFormat('M-Y', $this->date)->startOfMonth();

        foreach ($this->jobclasses as $key => $label) {
            $query = Manhour::where('date', $bulan->format('Y/m/d'))
                ->where('company', $this->company)
                ->where('department', $this->department)
                ->where('dept_group', $this->dept_group)
                ->where('job_class', $label);

            // 🔹 Jika checkbox dicentang atau kosong → hapus record lama
            if ($this->hide[$key] || (empty($this->manhours[$key]) && empty($this->manpower[$key]))) {
                $query->delete();
                continue;
            }

            // 🔹 Kalau create → buat baru
            if ($mode === 'create') {
                Manhour::create([
                    'date'             => $bulan->format('Y/m/d'),
                    'company_category' => $company_category,
                    'company'          => $this->company,
                    'department'       => $this->department,
                    'dept_group'       => $this->dept_group,
                    'job_class'        => $label,
                    'manhours'         => $this->manhours[$key],
                    'manpower'         => $this->manpower[$key],
                ]);
            }

            // 🔹 Kalau update → updateOrCreate
            if ($mode === 'update') {
                Manhour::updateOrCreate(
                    [
                        'id'               => $this->selectedId,
                    ],
                    [
                        'date'             => $bulan->format('Y/m/d'),
                        'company_category' => $company_category,
                        'company'          => $this->company,
                        'department'       => $this->department,
                        'dept_group'       => $this->dept_group,
                        'job_class'        => $label,
                        'manhours'         => $this->manhours[$key],
                        'manpower'         => $this->manpower[$key],
                    ]
                );
                $this->close_modal();
            }
        }


        // 🔹 Kirim Email setelah save
        $mailService = app(GraphMailService::class);

        $fromUserId = 'Yoman.Banea@archimining.com'; // user/email di O365
        $to         = 'yomandenis28@gmail.com';
        $subject    = $mode === 'create' ? 'Input Manhours Baru' : 'Update Data Manhours';
        $body       = "<p>Data manhours telah {$mode} untuk perusahaan <b>{$this->company}</b>, departemen <b>{$this->department}</b>.</p>";

        $mailService->sendMail($fromUserId, $to, $subject, $body);
        try {
        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'text'            => 'Gagal kirim email Graph: ' . $e->getMessage(),
                'duration'        => 5000,
                'destination'     => '/contact',
                'newWindow'       => true,
                'close'           => true,
                'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
            ]);
        }

        $this->dispatch('alert', [
            'text'            => $mode === 'create' ? "Data berhasil di input!!!" : "Data berhasil diperbarui!!!",
            'duration'        => 5000,
            'destination'     => '/contact',
            'newWindow'       => true,
            'close'           => true,
            'backgroundColor' => "linear-gradient(to right, #06b6d4, #22c55e)",
        ]);
    }

    public function store()
    {
        $this->saveManhours('create');
    }

    public function update($id)
    {
        $this->saveManhours('update', $id);
    }
    // Saat tombol hapus ditekan
    public function showDelete($id)
    {
        $this->selectedId = $id;
        $this->confirmingDelete = true; // buka modal konfirmasi
    }

    // Proses hapus data
    public function delete()
    {
        if ($this->selectedId) {
            Manhour::findOrFail($this->selectedId)->delete();

            // reset
            $this->selectedId = null;
            $this->confirmingDelete = false;

            // opsional: emit event untuk notifikasi / refresh tabel
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
    public function paginationView()
    {
        return 'paginate.pagination';
    }
}
