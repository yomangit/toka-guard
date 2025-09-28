<section class="w-full">

    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.header-hazard')
    <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center">
        <div class="mb-4 md:mb-0 z-30">
            <div class="tooltip tooltip-right md:tooltip-top " data-tip="Tambah Hazard">
                <a href="{{ route('hazard-form') }}" class="btn btn-square btn-primary btn-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                        <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="w-full md:w-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">
                <fieldset class="fieldset md:col-span-1">
                    <x-form.label label="rentang tanggal" required />
                    <div class="relative" wire:ignore x-data="{
                                fp: null,
                                initFlatpickr() {
                                    if (this.fp) this.fp.destroy();
                                    this.fp = flatpickr(this.$refs.tanggalInput2, {
                                        disableMobile: true,
                                        enableTime: false,
                                        altInput: true,
                                        altFormat: 'd-M-Y',
                                        dateFormat: 'd-m-Y',
                                        mode: 'range', // 👈 Tambahkan opsi ini
                                        onChange: (dates, str) => $wire.set('action_due_date', str),
                                    });
                                }
                            }" x-init="initFlatpickr(); Livewire.hook('message.processed', () => initFlatpickr());" x-ref="wrapper">
                        <input name="action_due_date" type="text" x-ref="tanggalInput2" wire:model.live="action_due_date" placeholder="Pilih Tanggal" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs {{ $errors->has('action_due_date') ? 'ring-1 ring-rose-500 focus:ring-rose-500 focus:border-rose-500' : '' }}" readonly />
                    </div>
                    <x-label-error :messages="$errors->get('action_due_date')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Tipe Bahaya" />
                    <select wire:model.live="filterEventType" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih Semua --</option>
                        @foreach ($eventTypes as $et)
                        <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                        @endforeach
                    </select>
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Jenis Bahaya" />
                    <select wire:model.live="filterEventSubType" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih Semua --</option>
                        @if ($filterEventType)
                        @foreach ($subTypes as $et)
                        <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                        @endforeach
                        @endif
                    </select>
                </fieldset>

                <fieldset>
                    <input id="department" value="department" wire:model="deptCont" class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]" label="PT. MSM & PT. TTN" />
                    <input id="company" value="company" wire:model="deptCont" class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                    <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor" />

                    <div class="hidden peer-checked/department:block ">
                        {{-- Department --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->

                            <input name="search" type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                            <!-- Dropdown hasil search -->
                            @if($showDropdown && count($departments) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik salah satu -->
                                <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($departments as $dept)
                                <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200 text-xs">
                                    {{ $dept->department_name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        @if($deptCont === 'department')
                        <x-label-error :messages="$errors->get('department_id')" />
                        @endif
                    </div>
                    <div class="hidden peer-checked/company:block ">
                        {{-- Contractor --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->
                            <input name="searchContractor" type="text" wire:model.live.debounce.300ms="searchContractor" placeholder="Cari kontraktor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showContractorDropdown && count($contractors) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($contractors as $contractor)
                                <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200 text-xs">
                                    {{ $contractor->contractor_name }}
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </div>
                        @if($deptCont === 'company')
                        <x-label-error :messages="$errors->get('contractor_id')" />
                        @endif
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    <div class="overflow-x-auto mt-4">
        <table class="table table-xs border text-sm px-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border">#</th>
                    <th class="border">reference</th>
                    <th class="border">Tipe Bahaya</th>
                    <th class="border">Jenis Bahaya</th>
                    <th class="border">Divisi Penanggung Jawab
                        <button class="btn btn-ghost btn-xs" popovertarget="divisi" style="anchor-name:divisi">
                            <span class="text-blue-600 text-xs">
                                {{-- SVG Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-filter-icon lucide-list-filter">
                                    <path d="M2 5h20" />
                                    <path d="M6 12h12" />
                                    <path d="M9 19h6" />
                                </svg>
                            </span>
                        </button>
                        {{-- Dropdown Menu --}}
                        <ul class="dropdown menu w-52 rounded-box bg-base-100 shadow-sm" popover id="divisi" style="position-anchor:divisi">

                            {{-- Loop Isi Dropdown --}}
                            @foreach ($availableStatuses as $status)
                            <li>
                                <label class="flex items-center mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterStatus" value="{{ $status }}" class="form-checkbox text-blue-600 rounded">
                                    <span class="ml-2 text-xs capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </th>
                    <th class="border relative">
                        Status
                        <button class="btn btn-ghost btn-xs" popovertarget="popover-1" style="anchor-name:--anchor-1">
                            <span class="text-blue-600 text-xs">
                                {{-- SVG Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-list-filter-icon lucide-list-filter">
                                    <path d="M2 5h20" />
                                    <path d="M6 12h12" />
                                    <path d="M9 19h6" />
                                </svg>
                            </span>
                        </button>
                        {{-- Dropdown Menu --}}
                        <ul class="dropdown menu w-52 rounded-box bg-base-100 shadow-sm" popover id="popover-1" style="position-anchor:--anchor-1">

                            {{-- Loop Isi Dropdown --}}
                            @foreach ($availableStatuses as $status)
                            <li>
                                <label class="flex items-center mb-1 cursor-pointer hover:bg-gray-100 p-1 rounded">
                                    <input type="checkbox" wire:model.live="filterStatus" value="{{ $status }}" class="form-checkbox text-blue-600 rounded">
                                    <span class="ml-2 text-xs capitalize">{{ str_replace('_', ' ', $status) }}</span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
    </div>
    </th>
    <th class="border">Pelapor</th>
    <th class="border">Tanggal</th>
    <th class="flex-col border text-center">
        <p>Action</p>
        <p>Total/Open</p>
    </th>
    </tr>


    </thead>
    <tbody>
        @forelse ($reports as $no => $report)
        <tr class="hover:bg-gray-50">
            <td class="border">{{ $reports->firstItem()+$no }}</td>
            <td class="border">
                @can('view', $report)
                <a href="{{ route('hazard-detail', $report) }}" class="text-blue-600 text-xs hover:underline">{{ $report->no_referensi  ?? '-' }}</a>
                @else
                <span class="text-gray-400 text-xs cursor-not-allowed">{{ $report->no_referensi  ?? '-' }}</span>
                @endcan
            </td>
            <td class="border">{{ $report->eventType->event_type_name  ?? '-' }}</td>
            <td class="border">{{ $report->eventSubType->event_sub_type_name  ?? '-' }}</td>
            <td class="border">{{ $report->department->department_name ?? $report->contractor->contractor_name }}</td>
            <td class="border">
                <span class="text-xs uppercase px-2  rounded
                                @if($report->status == 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($report->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($report->status == 'pending') bg-orange-100 text-orange-800
                                @elseif($report->status == 'closed') bg-green-100 text-green-800
                                @endif">
                    {{ str_replace('_', ' ', $report->status) }}
                </span>
            </td>
            <td class="border">{{ $report->pelapor->name ?? $report->manualPelaporName }}</td>
            <td class="border">{{ \Carbon\Carbon::parse($report->tanggal)->format('d M Y') }}</td>
            <td class="border text-center">
                {{ $report->total_due_dates }} / {{ $report->pending_actual_closes }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center text-gray-500 py-4">Tidak ada laporan ditemukan.</td>
        </tr>
        @endforelse
    </tbody>
    </table>
    </div>
    {{ $reports->links() }}
</section>
