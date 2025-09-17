<section class="w-full">
    <x-toast />
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> --}}
    @include('partials.manhours-heading')
    {{-- @livewire('hazard.hazard-form') --}}
    <x-manhours.layout>
        {{-- <livewire:hazard.hazard-report-panel /> --}}

        <form wire:submit.prevent="submit">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <fieldset class="fieldset">
                    <x-form.label label="Tipe Bahaya" required />
                    <select wire:model.live="tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach ($eventTypes as $et )
                        <option value="{{ $et->id }}">{{ $et->event_type_name }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('tipe_bahaya')" />
                </fieldset>
                <fieldset class="fieldset">
                    <x-form.label label="Sub Tipe Bahaya" required />
                    <select wire:model.live="sub_tipe_bahaya" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @if ($tipe_bahaya)
                        @foreach ($subTypes as $et )
                        <option value="{{ $et->id }}">{{ $et->event_sub_type_name }}</option>
                        @endforeach
                        @endif

                    </select>
                    <x-label-error :messages="$errors->get('sub_tipe_bahaya')" />
                </fieldset>

                <fieldset class="fieldset">
                    <x-form.label label="Dilaporkan Oleh" required />
                    <div class="relative">
                        <!-- Input Search -->
                        <input type="text" wire:model.live.debounce.300ms="searchPelapor" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <!-- Dropdown hasil search -->
                        @if($showPelaporDropdown)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik -->
                            <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @if(count($pelapors) > 0)
                            @foreach($pelapors as $pelapor)
                            <li wire:click="selectPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $pelapor->name }}
                            </li>
                            @endforeach
                            @else
                            <!-- Jika tidak ada hasil & belum mode manual -->
                            @if(!$manualPelaporMode)
                            <li wire:click="enableManualPelapor" class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                Tidak ditemukan, tambah pelapor manual
                            </li>
                            @endif
                            @endif
                            <!-- Input manual jika mode manual aktif -->
                            @if($manualPelaporMode)
                            <li class="p-2">
                                <div class="relative w-full">
                                    <input type="text" wire:model.live="manualPelaporName" placeholder="Masukkan nama pelapor..." class="input input-bordered w-full pr-20 focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                                    <div class="!absolute top-1/2 -translate-y-1/2 right-0 z-20">
                                        <flux:button size="xs" wire:click="addPelaporManual" icon="plus" variant="primary">
                                            Tambah
                                        </flux:button>
                                    </div>
                                </div>
                            </li>

                            @endif
                        </ul>
                        @endif
                    </div>
                    <!-- Error Message -->
                    @if($manualPelaporMode)
                    <x-label-error :messages="$errors->get('manualPelaporName')" />
                    @else
                    <x-label-error :messages="$errors->get('pelapor_id')" />
                    @endif
                </fieldset>
                <fieldset>
                    <input id="department" value="department" wire:model="deptCont" class="peer/department radio radio-xs radio-accent" type="radio" name="deptCont" checked />
                    <x-form.label for="department" class="peer-checked/department:text-accent text-[10px]" label="PT. MSM & PT. TTN" required />
                    <input id="company" value="company" wire:model="deptCont" class="peer/company radio radio-xs radio-primary" type="radio" name="deptCont" />
                    <x-form.label for="company" class="peer-checked/company:text-primary" label="Kontraktor" required />

                    <div class="hidden peer-checked/department:block ">
                        {{-- Department --}}
                        <div class="relative mb-1">
                            <!-- Input Search -->

                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari departemen..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs " />
                            <!-- Dropdown hasil search -->
                            @if($showDropdown && count($departments) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik salah satu -->
                                <div wire:loading wire:target="selectDepartment" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($departments as $dept)
                                <li wire:click="selectDepartment({{ $dept->id }}, '{{ $dept->department_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
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
                            <input type="text" wire:model.live.debounce.300ms="searchContractor" placeholder="Cari kontraktor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showContractorDropdown && count($contractors) > 0)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectContractor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @foreach($contractors as $contractor)
                                <li wire:click="selectContractor({{ $contractor->id }}, '{{ $contractor->contractor_name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
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
                <fieldset class="fieldset">
                    <x-form.label label="Penanggung Jawab Area" required />
                    <select wire:model.live="penanggungJawab" class="select select-xs select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                        <option value="">-- Pilih --</option>
                        @foreach($penanggungJawabOptions as $pj)
                        <option value="{{ $pj['id'] }}">{{ $pj['name'] }}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('penanggungJawab')" />
                </fieldset>

                <fieldset class="fieldset ">
                    <x-form.label label="Lokasi" required />
                    <div class="relative">
                        <!-- Input Search -->
                        <input type="text" wire:model.live.debounce.300ms="searchLocation" placeholder="Cari Lokasi..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        <!-- Dropdown hasil search -->
                        @if($showLocationDropdown && count($locations) > 0)
                        <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                            <!-- Spinner ketika klik -->
                            <div wire:loading wire:target="selectLocation" class="p-2 text-center">
                                <span class="loading loading-spinner loading-sm text-secondary"></span>
                            </div>
                            @foreach($locations as $loc)
                            <li wire:click="selectLocation({{ $loc->id }}, '{{ $loc->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                {{ $loc->name }}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                    <x-label-error :messages="$errors->get('location_id')" />
                </fieldset>

                {{-- Lokasi spesifik muncul hanya jika lokasi utama sudah dipilih --}}
                @if($location_id)
                <fieldset class="fieldset">
                    <x-form.label label="Lokasi Spesifik" required />
                    <input type="text" wire:model.live="location_specific" placeholder="Masukkan detail lokasi spesifik..." class=" input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('location_specific')" />
                </fieldset>
                @endif
                <fieldset class="fieldset relative">
                    <x-form.label label="Tanggal & Waktu" required />
                    <div class="relative" wire:ignore x-data="{
                            fp: null,
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput, {
                                    disableMobile: true,
                                    enableTime: true,
                                    dateFormat: 'd-m-Y H:i',
                                    clickOpens: true,
                                    appendTo: this.$refs.wrapper,
                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('tanggal', dateStr);
                                    }
                                });
                            }
                        }" x-ref="wrapper" x-init="
                            initFlatpickr();
                            Livewire.hook('message.processed', () => {
                                initFlatpickr();
                            });
                        ">
                        <input type="text" x-ref="tanggalInput" wire:model.live='tanggal' placeholder="Pilih Tanggal dan Waktu..." readonly class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    </div>
                    <x-label-error :messages="$errors->get('tanggal')" />
                </fieldset>

            </div>
            <fieldset class="fieldset mb-4">
                <x-form.label label="Deskripsi" required />
                <div wire:ignore>
                    <textarea id="ckeditor-description"></textarea>
                </div>
                <!-- Hidden input untuk binding Livewire -->
                <input type="hidden" wire:model.live="description" id="description">
                <x-label-error :messages="$errors->get('description')" />
            </fieldset>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">
                <fieldset class=" fieldset">
                    <x-form.label label="Dokumentasi Sebelum Tidakan perbaikan langsung" />
                    <label wire:ignore for="upload-deskripsi" class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                        <!-- Tombol custom -->
                        <span class="btn btn-info btn-xs">
                            Pilih file atau gambar
                        </span>
                        <!-- Nama file -->
                        <span id="file-name" class="text-xs text-gray-500">
                            Belum ada file
                        </span>
                    </label>
                    @if ($doc_deskripsi)
                    @if (in_array($doc_deskripsi->getClientOriginalExtension(), ['jpg','jpeg','png']))
                    <img src="{{ $doc_deskripsi->temporaryUrl() }}" class="mt-2 w-40 h-auto rounded border" />
                    @else
                    <p class="mt-2 text-sm text-gray-600">File: {{ $doc_deskripsi->getClientOriginalName() }}</p>
                    @endif
                    @endif
                    <!-- Input asli (disembunyikan) -->
                    <input id="upload-deskripsi" wire:model.live='doc_deskripsi' type="file" class="hidden" onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                    <x-label-error :messages="$errors->get('doc_deskripsi')" />
                </fieldset>
            </div>
            <fieldset class="fieldset mb-4">
                <label class="block"></label>
                <x-form.label label="Tindakan perbaikan langsung" required />
                <div wire:ignore>
                    <textarea id="ckeditor-immediate_corrective_action"></textarea>
                </div>
                <!-- Hidden input untuk binding Livewire -->
                <input type="hidden" wire:model.live="immediate_corrective_action" id="immediate_corrective_action">
                <x-label-error :messages="$errors->get('immediate_corrective_action')" />
            </fieldset>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 ">

                <fieldset class=" fieldset">
                    <x-form.label label="Dokumentasi Sesudah Tidakan perbaikan langsung" />
                    <label class="block"></label>
                    <label wire:ignore for="upload-corrective" class="flex items-center gap-2 cursor-pointer border border-info rounded  hover:ring-1 hover:border-info hover:ring-info hover:outline-hidden">
                        <!-- Tombol custom -->
                        <span class="btn btn-info btn-xs">
                            Pilih file atau gambar
                        </span>
                        <!-- Nama file -->
                        <span id="file-name-corrective" class="text-xs text-gray-500">
                            Belum ada file
                        </span>
                    </label>
                    @if ($doc_corrective)
                    @if (in_array($doc_corrective->getClientOriginalExtension(), ['jpg','jpeg','png']))
                    <img src="{{ $doc_corrective->temporaryUrl() }}" class="mt-2 w-40 h-auto rounded border" />
                    @else
                    <p class="mt-2 text-sm text-gray-600">File: {{ $doc_corrective->getClientOriginalName() }}</p>
                    @endif
                    @endif
                    <!-- Input asli (disembunyikan) -->
                    <input id="upload-corrective" wire:model.live='doc_corrective' type="file" class="hidden" onchange="document.getElementById('file-name-corrective').textContent = this.files[0]?.name ?? 'Belum ada file'" />
                    <x-label-error :messages="$errors->get('doc_corrective')" />
                </fieldset>
            </div>
            <div class="grid grid-cols-1 gap-4  border border-gray-300 p-4 rounded">

                <div class="flex gap-2 ">
                    <fieldset class="fieldset mb-4">
                        <label class="block"></label>
                        <x-form.label label="Deskripsi tindakan" required />
                        <div wire:ignore>
                            <textarea id="ckeditor-action_description"></textarea>
                        </div>
                        <!-- Hidden input untuk binding Livewire -->
                        <input type="hidden" wire:model.live="action_description" id="action_description">
                        <x-label-error :messages="$errors->get('action_description')" />
                    </fieldset>
                    <fieldset class="fieldset relative">
                        <x-form.label label="Tanggal & Waktu" required />
                        <div class="relative" wire:ignore x-data="{
                            fp: null,
                            initFlatpickr() {
                                if (this.fp) this.fp.destroy();
                                this.fp = flatpickr(this.$refs.tanggalInput2, {
                                    disableMobile: true,
                                    enableTime: false,
                                    dateFormat: 'd-m-Y',
                                    clickOpens: true,
                                    appendTo: this.$refs.wrapper,
                                    onChange: (selectedDates, dateStr) => {
                                        this.$wire.set('action_due_date', dateStr);
                                    }
                                });
                            }
                                }" x-ref="wrapper" x-init="
                                    initFlatpickr();
                                    Livewire.hook('message.processed', () => {
                                        initFlatpickr();
                                    });
                                ">
                            <input type="text" x-ref="tanggalInput2" wire:model.live='action_due_date' placeholder="Pilih Tanggal " readonly class="input input-bordered cursor-pointer w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                        </div>
                        <x-label-error :messages="$errors->get('tanggal')" />
                    </fieldset>
                    <fieldset class="fieldset">
                        <x-form.label label="Dilaporkan Oleh" required />
                        <div class="relative">
                            <!-- Input Search -->
                            <input type="text" wire:model.live.debounce.300ms="searchActResponsibility" placeholder="Cari Nama Pelapor..." class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                            <!-- Dropdown hasil search -->
                            @if($showActPelaporDropdown)
                            <ul class="absolute z-10 bg-base-100 border rounded-md w-full mt-1 max-h-60 overflow-auto shadow">
                                <!-- Spinner ketika klik -->
                                <div wire:loading wire:target="selectPelapor" class="p-2 text-center">
                                    <span class="loading loading-spinner loading-sm text-secondary"></span>
                                </div>
                                @if(count($pelaporsAct) > 0)
                                @foreach($pelaporsAct as $pelapor)
                                <li wire:click="selectActPelapor({{ $pelapor->id }}, '{{ $pelapor->name }}')" class="px-3 py-2 cursor-pointer hover:bg-base-200">
                                    {{ $pelapor->name }}
                                </li>
                                @endforeach
                                @else
                                <!-- Jika tidak ada hasil & belum mode manual -->
                                @if(!$manualActPelaporMode)
                                <li wire:click="enableManualActPelapor" class="px-3 py-2 cursor-pointer text-warning hover:bg-base-200">
                                    Tidak ditemukan, tambah pelapor manual
                                </li>
                                @endif
                                @endif
                                <!-- Input manual jika mode manual aktif -->
                                @if($manualActPelaporMode)
                                <li class="p-2">
                                    <div class="relative w-full">
                                        <input type="text" wire:model.live="manualActPelaporName" placeholder="Masukkan nama..." class="input input-bordered w-full pr-20 focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                                        <div class="!absolute top-1/2 -translate-y-1/2 right-0 z-20">
                                            <flux:button size="xs" wire:click="addActPelaporManual" icon="plus" variant="primary">
                                                Tambah
                                            </flux:button>
                                        </div>
                                    </div>
                                </li>

                                @endif
                            </ul>
                            @endif
                        </div>
                        <!-- Error Message -->
                        @if($manualPelaporMode)
                        <x-label-error :messages="$errors->get('manualPelaporName')" />
                        @else
                        <x-label-error :messages="$errors->get('pelapor_id')" />
                        @endif
                    </fieldset>
                    <button type="button" wire:click="addAction" class="btn btn-primary">Tambah</button>
                </div>
                <ul class="mt-3 space-y-2">
                    @forelse($actions as $index => $act)
                    <li class="flex items-center justify-between border p-2 rounded">
                        <div>
                            <p><strong>{!! $act['description'] !!}</strong></p>
                            <p class="text-sm text-gray-500">
                                Deadline: {{ $act['due_date'] }} |
                                PIC: {{ optional(\App\Models\User::find($act['responsible_id']))->name }}
                            </p>
                        </div>
                        <button type="button" wire:click="removeAction({{ $index }})" class="btn btn-sm btn-error">Hapus</button>
                    </li>
                    @empty
                    <li class="text-gray-500 text-sm">Belum ada tindakan lanjutan ditambahkan.</li>
                    @endforelse
                </ul>


            </div>
            <div class="grid grid-cols-1 md:grid-cols-2  gap-4 mb-4 border border-gray-300 p-4 rounded">
                {{-- KEY WORD --}}
                <fieldset>
                    <input id="kta" value="kta" wire:model.live="keyWord" class="peer/kta radio radio-xs radio-accent" type="radio" name="keyWord" checked />
                    <x-form.label for="kta" class="peer-checked/kta:text-accent text-[10px]" label="Kondisi Tidak Aman" required />
                    <input id="tta" value="tta" wire:model.live="keyWord" class="peer/tta radio radio-xs radio-primary" type="radio" name="keyWord" />
                    <x-form.label for="tta" class="peer-checked/tta:text-primary text-[10px]" label="Tindakan Tidak Aman" required />
                    <div class="hidden peer-checked/kta:block mt-1">
                        <select wire:model.live="kondisi_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih Kondisi Tidak Aman --</option>
                            @foreach ($ktas as $kta)
                            <option value="{{ $kta->id }}">{{ $kta->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="hidden peer-checked/tta:block mt-1">
                        <select wire:model.live="tindakan_tidak_aman" class="select select-xs mb-1 select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden">
                            <option value="">-- Pilih Tidakan Tidak Aman --</option>
                            @foreach ($ttas as $tta)
                            <option value="{{ $tta->id }}">{{ $tta->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if($keyWord === 'kta')
                    <x-label-error :messages="$errors->get('kondisi_tidak_aman')" />
                    @endif
                    @if($keyWord === 'tta')
                    <x-label-error :messages="$errors->get('tindakan_tidak_aman')" />
                    @endif

                </fieldset>
            </div>
            <div class="flex flex-col md:flex-row gap-2">

                {{-- Kolom Likelihood & Consequence --}}
                <div class=" space-y-4 md:grow">
                    {{-- Consequence --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Consequence" required />
                        <select wire:model.live="consequence_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach ($consequencess as $cons)
                            <option value="{{ $cons->id }}">{{ $cons->name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('consequence_id')" />

                        @if($consequence_id)
                        @php
                        $selectedConsequence = $consequencess->firstWhere('id', $consequence_id);
                        @endphp
                        @if($selectedConsequence)
                        <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                            {{ $selectedConsequence->description ?? 'Tidak ada deskripsi' }}
                        </div>
                        @endif
                        @endif
                    </fieldset>
                    {{-- Likelihood --}}
                    <fieldset class="fieldset ">
                        <x-form.label label="Likelihood" required />
                        <select wire:model.live="likelihood_id" class="select select-xs md:select-xs select-bordered w-full md:max-w-md focus:ring-1 focus:border-info focus:ring-info focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach ($likelihoodss as $like)
                            <option value="{{ $like->id }}">{{ $like->name }}</option>
                            @endforeach
                        </select>
                        <x-label-error :messages="$errors->get('likelihood_id')" />

                        @if($likelihood_id)
                        @php
                        $selectedLikelihood = $likelihoodss->firstWhere('id', $likelihood_id);
                        @endphp
                        @if($selectedLikelihood)
                        <div class="mt-1 text-sm text-gray-600 h-20 overflow-y-auto border rounded p-2 bg-gray-50">
                            {{ $selectedLikelihood->description ?? 'Tidak ada deskripsi' }}
                        </div>
                        @endif
                        @endif
                    </fieldset>


                </div>

                {{-- Kolom Risk Matrix --}}
                <div class="overflow-x-auto  flex-none ">
                    <div role="tablist" class="flex">


                    </div>
                    <table class="table table-xs w-60">
                        <thead>
                            <tr class="text-center text-[9px]">
                                <td class=" border-1">Level</td>
                                <td class="rotate_text border-1 bg-emerald-500">Low</td>
                                <td class="rotate_text border-1 bg-yellow-500">Moderate</td>
                                <td class="rotate_text border-1 bg-orange-500">High</td>
                                <td class="rotate_text border-1 bg-rose-500">Extreme</td>
                                <td class="rotate_text border-1 bg-gray-100">Closed</td>
                            </tr>
                            <tr class="text-center text-[9px]">
                                <th class="border-1">Likelihood ↓ / Consequence →</th>
                                @foreach ($consequences as $c)

                                <th class="rotate_text border-1">{{ $c->name }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($likelihoods as $l)
                            <tr class="text-center text-xs w-32">

                                <td class=" font-bold w-1 border-1">{{ $l->name }}</td>
                                @foreach ($consequences as $c)
                                @php
                                $cell = App\Models\RiskMatrixCell::where('likelihood_id', $l->id)->where('risk_consequence_id', $c->id)->first() ?? null;
                                $score = $l->level * $c->level;
                                $severity = $cell?->severity ?? '';
                                $color = match($severity) {
                                'Low' => 'bg-emerald-500',
                                'Moderate' => 'bg-yellow-500',
                                'High' => 'bg-orange-500',
                                'Extreme' => 'bg-rose-500',
                                default => 'bg-gray-100',
                                };
                                @endphp
                                <td class="border cursor-pointer  @if($likelihood_id == $l->id && $consequence_id == $c->id) border-2 border-stone-500 @endif">
                                    <span wire:click="edit({{ $l->id }}, {{ $c->id }})" class="btn btn-square btn-xs   {{ $color}}">{{ Str::upper(substr($severity, 0, 1)) }}</span>
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if ($RiskAssessment !=null)
            <table class="table table-xs mt-4">

                <tr>
                    <th class="w-40 text-xs border-2 border-slate-400">Potential Risk Rating</th>
                    <td class="pl-2 text-xs border-2 border-slate-400">
                        {{ $RiskAssessment->name }}</td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-2 border-slate-400">Notify</th>
                    <td class="pl-2 text-xs border-2 border-slate-400">
                        {{ $RiskAssessment->reporting_obligation }}</td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-2 border-slate-400">Deadline</th>
                    <td class="pl-2 text-xs border-2 border-slate-400">{{ $RiskAssessment->notes }}</td>
                </tr>
                <tr>
                    <th class="w-40 text-xs border-2 border-slate-400">Coordinator</th>
                    <td class="pl-2 text-xs border-2 border-slate-400">
                        {{ $RiskAssessment->coordinator }}
                    </td>
                </tr>


            </table>
            @endif
            <flux:button size="xs" type="submit" icon:trailing="send" variant="primary">Kirim Laporan</flux:button>
        </form>
    </x-manhours.layout>
</section>
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-description'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#description').value = data;

                    // Kirim ke Livewire
                    @this.set('description', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-immediate_corrective_action'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-immediate_corrective_action').value = data;

                    // Kirim ke Livewire
                    @this.set('immediate_corrective_action', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
<script>
    document.addEventListener('livewire:navigated', () => {
        ClassicEditor
            .create(document.querySelector('#ckeditor-action_description'), {
                toolbar: [
                    // 'heading', '|'
                    , 'bold', 'italic', 'bulletedList', 'numberedList', '|'
                    , 'undo', 'redo'
                ]
                , removePlugins: ['ImageUpload', 'EasyImage', 'MediaEmbed'] // buang plugin gambar
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    // Update ke hidden input
                    const data = editor.getData();
                    document.querySelector('#ckeditor-action_description').value = data;

                    // Kirim ke Livewire
                    @this.set('action_description', data);
                });
            })
            .catch(error => {
                console.error(error);
            });
    });

</script>
@endpush
