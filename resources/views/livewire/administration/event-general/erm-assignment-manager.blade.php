<section class="w-full">
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    @include('partials.event-general-head')
    <!-- name of each tab group should be unique -->
    <x-tabs-event.layout>
        <div class="flex justify-between">
            <div>
                <flux:tooltip content="tambah data" position="top">
                    <flux:button size="xs" wire:click='open_modal' icon="add-icon" variant="primary"></flux:button>
                </flux:tooltip>

            </div>
            <div>
                <flux:input size='xs' icon="magnifying-glass" wire:model.live='search_event_category' placeholder="cari erm..." />
            </div>
        </div>
        <div class="overflow-x-auto ">
            <table class="table table-xs">
                <thead class="text-center">
                    <tr>
                        <th>#</th>
                        <th>{{ __('Nama ERM ') }}</th>
                        <th>{{ __('Departement') }}</th>
                        <th>{{ __('Company') }}</th>
                        <th>{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($ErmAssignment as $no => $erm)
                    <tr>
                        <th>{{ $ErmAssignment->firstItem() + $no }}</th>
                        <th>{{ $erm->user->name }}</th>
                        <th>{{ $erm->department_id? $erm->department->department_name:'-'  }}</th>
                        <th>{{ $erm->company_id? $erm->company->company_name:'-'  }}</th>
                        <th class='flex justify-center flex-row gap-2'>
                            <flux:tooltip content="edit" position="top">
                                <flux:button wire:click="open_modal_edit({{ $erm->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                            </flux:tooltip>
                            <flux:modal.trigger name="delete-company">
                                <flux:tooltip content="hapus" position="top">
                                    <flux:button wire:click="showDelete({{ $erm->id }})" size="xs" icon="trash" variant="danger"></flux:button>
                                </flux:tooltip>
                            </flux:modal.trigger>
                        </th>
                    </tr>
                    @endforeach

                </tbody>
                
            </table>
        </div>
        <flux:modal name="ERM-Asign">
            <form wire:submit.prevent='assign' class='grid justify-items-stretch'>
                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border p-4 sm:justify-self-center">
                    <legend class="fieldset-legend">ERM Register</legend>

                    <fieldset>

                        <input id="department" class="peer/department radio radio-xs radio-accent" type="radio" name="status" checked /> <label for="department" class="peer-checked/department:text-accent">Departemen</label>
                        <input id="company" class="peer/company radio radio-xs radio-primary" type="radio" name="status" /> <label for="company" class="peer-checked/company:text-primary">Kontraktor</label>
                        <div class="hidden peer-checked/department:block mt-1">
                            {{-- Department --}}
                            {{-- <x-label-no-req>{{ __('Department') }} </x-label-no-req> --}}
                            <flux:select size="xs" wire:model.live="selectedDepartment" placeholder="Pilih Departemen...">
                                @foreach ($departments as $dept)
                                <flux:select.option value="{{ $dept->id }}">{{ $dept->department_name }}
                                </flux:select.option>
                                @endforeach
                            </flux:select>
                            <x-label-error :messages="$errors->get('selectedDepartment')" />
                        </div>
                        <div class="hidden peer-checked/company:block mt-1">
                            {{-- Company --}}
                            {{-- <x-label-req>{{ __('Company') }} </x-label-req> --}}
                            <flux:select size="xs" wire:model.live="selectedCompany" placeholder="Pilih Kontraktor...">
                                 @foreach ($contractors as $contractor)
                                <flux:select.option value="{{ $contractor->id }}">{{ $contractor->contractor_name }}
                                </flux:select.option>
                                @endforeach
                            </flux:select>
                            <x-label-error :messages="$errors->get('selectedCompany')" />
                        </div>
                    </fieldset>
                    {{-- User --}}
                    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border px-2 sm:justify-self-center">
                        <legend class="fieldset-legend after:-ml-1 after:-mt-2 after:text-[9px] after:text-rose-500 after:content-['*']">
                            Pilih User sebagai ERM</legend>
                        <div class=" w-full grid justify-items-stretch">
                            <div class="justify-self-end w-40">
                                <flux:input size='xs' icon="magnifying-glass" placeholder="Cari user..." wire:model.live='search_user' />
                            </div>
                        </div>
                        <flux:select size="xs" wire:model.live="selectedUsers" multiple class="h-32 overflow-y-scroll">
                            @foreach ($users as $user)
                            <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <x-label-error :messages="$errors->get('selectedUsers')" />
                    </fieldset>
                </fieldset>
                <div class="modal-action">
                    <flux:button size="xs" type="submit" icon="save-icon" variant="primary">Save</flux:button>
                    <flux:button size="xs" wire:click='close_modal' icon="close-icon" variant="danger">Close
                    </flux:button>
                </div>
            </form>
        </flux:modal>
        <flux:modal name="ERM-edit">
            <form wire:submit.prevent='update' class='grid justify-items-stretch'>
                <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border p-4 sm:justify-self-center">
                    <legend class="fieldset-legend">Edit ERM</legend>

                    <fieldset>

                        <input id="department" class="peer/department radio radio-xs radio-accent" type="radio" name="status" checked /> <label for="department" class="peer-checked/department:text-accent">Department</label>
                        <input id="company" class="peer/company radio radio-xs radio-primary" type="radio" name="status" /> <label for="company" class="peer-checked/company:text-primary">Company</label>
                        <div class="hidden peer-checked/department:block mt-1">
                            {{-- Department --}}
                            {{-- <x-label-no-req>{{ __('Department') }} </x-label-no-req> --}}
                            <flux:select size="xs" wire:model.live="selectedDepartment" placeholder="Choose Department...">
                                @foreach ($departments as $dept)
                                <flux:select.option value="{{ $dept->id }}">{{ $dept->department_name }}
                                </flux:select.option>
                                @endforeach
                            </flux:select>
                            <x-label-error :messages="$errors->get('selectedDepartment')" />
                        </div>
                        <div class="hidden peer-checked/company:block mt-1">
                            {{-- Company --}}
                            {{-- <x-label-req>{{ __('Company') }} </x-label-req> --}}
                            <flux:select size="xs" wire:model.live="selectedCompany" placeholder="Choose Company...">
                                @foreach ($contractors as $contractor)
                                <flux:select.option value="{{ $contractor->id }}">{{ $contractor->contractor_name }}
                                </flux:select.option>
                                @endforeach
                            </flux:select>
                            <x-label-error :messages="$errors->get('selectedCompany')" />
                        </div>
                    </fieldset>
                    {{-- User --}}
                    <fieldset class="fieldset bg-base-200 border-base-300 rounded-box w-xs sm:w-sm border px-2 sm:justify-self-center">
                        <legend class="fieldset-legend after:-ml-1 after:-mt-2 after:text-[9px] after:text-rose-500 after:content-['*']">
                            Pilih User sebagai ERM</legend>
                        <div class=" w-full grid justify-items-stretch">
                            <div class="justify-self-end w-40">
                                <flux:input size='xs' icon="magnifying-glass" placeholder="Cari user..." wire:model.live='search_user' />
                            </div>
                        </div>
                        <flux:select size="xs" wire:model.live="user_id" class=" overflow-y-scroll">
                            @foreach ($users as $user)
                            <flux:select.option value="{{ $user->id }}">{{ $user->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <x-label-error :messages="$errors->get('user_id')" />
                    </fieldset>
                </fieldset>
                <div class="modal-action">
                    <flux:button size="xs" type="submit" icon="save-icon" variant="primary">Save</flux:button>
                    <flux:button size="xs" wire:click='close_modal' icon="close-icon" variant="danger">Close
                    </flux:button>
                </div>
            </form>
        </flux:modal>
        <flux:modal name="delete-erm" class="min-w-[22rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Delete project?</flux:heading>
                    <flux:text class="mt-2">
                        <p>You're about to delete this {{ $name }}.</p>
                        <p>This action cannot be reversed.</p>
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click='delete' size='xs' variant="danger">Delete </flux:button>
                </div>
            </div>
        </flux:modal>

    </x-tabs-event.layout>


</section>
