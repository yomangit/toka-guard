<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.header-hazard')
    <div class="flex flex-col md:flex-row md:justify-between ">
        <div class="tooltip tooltip-right md:tooltip-top">
            <div class="tooltip-content z-40">
                <div class="animate-bounce text-orange-400  text-sm font-black">Tambah Hazard</div>
            </div>
            <a href="{{ route('hazard-form') }}" class="btn btn-square btn-primary btn-xs">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <fieldset class="fieldset">
                    <x-form.label label="Tipe Bahaya"  />
                    <select wire:model.live="filterEventType" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden ">
                        <option value="">-- Pilih --</option>
                        @foreach ($eventTypes as $et )
                        <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                        @endforeach
                    </select>
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Jenis Bahaya"  />
                    <select wire:model.live="filterEventSubType" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @if ($filterEventType)
                        @foreach ($subTypes as $et )
                        <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                        @endforeach
                        @endif

                    </select>
                </fieldset>
            <fieldset>
                <input id="department" value="department" wire:model="deptCont" class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]" label="PT. MSM & PT. TTN"  />
                <input id="company" value="company" wire:model="deptCont" class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor"  />

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
    <div class="overflow-auto mt-4">
        <table class="table table-xs border text-sm px-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">reference</th>
                    <th class="border px-2 py-1">Status</th>
                    <th class="border px-2 py-1">Pelapor</th>
                    <th class="border px-2 py-1">Tanggal</th>
                    <th class="border px-2 py-1">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="border px-2 py-1">{{ $report->id }}</td>
                    <td class="border px-2 py-1">{{ $report->no_referensi  ?? '-' }}</td>
                    <td class="border px-2 py-1">
                        <span class="text-xs uppercase px-2 py-1 rounded
                                @if($report->status == 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($report->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($report->status == 'pending') bg-orange-100 text-orange-800
                                @elseif($report->status == 'closed') bg-green-100 text-green-800
                                @endif">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </td>
                    <td class="border px-2 py-1">{{ $report->pelapor->name ?? $report->manualPelaporName }}</td>
                    <td class="border px-2 py-1">{{ $report->created_at->format('d M Y') }}</td>
                    <td>
                        @can('view', $report)
                        <a href="{{ route('hazard-detail', $report) }}" class="text-blue-600 text-sm hover:underline">Detail</a>
                        @else
                        <span class="text-gray-400 text-sm cursor-not-allowed">Detail</span>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada laporan ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $reports->links() }}
</section>
