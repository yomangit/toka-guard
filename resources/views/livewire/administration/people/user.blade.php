<section class="w-full">
    <x-toast />
    <x-tabs-relation.layout>
        <div class="p-4">
            <div class="flex justify-between items-center mb-4">
                <flux:tooltip content="Bulk Update" position="top">
                    <flux:button size="xs" variant="primary" icon='refresh-cw' wire:click="$set('showBulkUpdateModal', true)"></flux:button>
                </flux:tooltip>
                <div>

                    <flux:tooltip content="tambah data" position="top">
                        <flux:button size="xs" wire:click="create" icon="add-icon" variant="primary"></flux:button>
                    </flux:tooltip>
                    <flux:tooltip content="Import data" position="top">
                        <flux:button size="xs" wire:click="$set('showImportModal', true)" icon="upload" variant="subtle"></flux:button>
                    </flux:tooltip>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-xs">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" wire:model.live="selectAll">
                            </th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Username</th>
                            <th>Department</th>
                            <th>Employee ID</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $user->id }}" wire:model.live="selectedUsers">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->gender }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->department_name }}</td>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->email }}</td>
                            <td class="flex gap-2">
                                <!-- Edit -->

                                <div class="tooltip tooltip-right ">
                                    <div class="tooltip-content ">
                                        <div class="animate-bounce text-warning text-xs font-black">Edit</div>
                                    </div>
                                    <flux:button wire:click="edit({{ $user->id }})" size="xs" icon="pencil-square" variant="subtle"></flux:button>
                                </div>
                                <!-- Delete -->
                                <div class="tooltip tooltip-right ">
                                    <div class="tooltip-content ">
                                        <div class="animate-bounce text-error text-xs font-black">Delete</div>
                                    </div>
                                    <flux:button wire:click="confirmDelete({{ $user->id }})" size="xs" icon="trash" variant="danger"></flux:button>
                                </div>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </x-tabs-relation.layout>
    {{ $users->links() }}

    {{-- Create/Edit Modal --}}
    <dialog class="modal" @if($showModal) open @endif>
        <div class="modal-box w-11/12 max-w-2xl">
            <h3 class="font-bold text-lg">{{ $userId ? 'Edit User' : 'Add User' }}</h3>

            <div class="grid grid-cols-2 gap-4 mt-4">

                <fieldset class="fieldset">
                    <label class="block">Nama</label>
                    <input type="text" wire:model.live="name" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('name')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Jenis Kelamin</label>
                    <select wire:model.live="gender" class="select select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki - laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                    <x-label-error :messages="$errors->get('gender')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Tanggal Lahir</label>
                    <input type="text" readonly id="date_birth" wire:model="date_birth" class=" cursor-pointer input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" placeholder="Pilih tanggal lahir" x-data x-init="flatpickr($refs.input, {dateFormat: 'Y-m-d'})" x-ref="input" />
                    <x-label-error :messages="$errors->get('date_birth')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Username</label>
                    <input type="text" wire:model.live="username" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('username')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Department</label>
                    <input type="text" wire:model.live="department_name" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('department_name')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Employee ID</label>
                    <input type="text" wire:model.live="employee_id" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('employee_id')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Tanggal Masuk</label>
                    <input type="text" readonly id="date_commenced" wire:model="date_commenced" class="cursor-pointer input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" placeholder="Pilih tanggal masuk" x-data x-init="flatpickr($refs.input, {dateFormat: 'Y-m-d'})" x-ref="input" />
                    <x-label-error :messages="$errors->get('date_commenced')" />
                </fieldset>

                <fieldset class="fieldset">
                    <label class="block">Email</label>
                    <input type="email" wire:model.live="email" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />
                    <x-label-error :messages="$errors->get('email')" />
                </fieldset>
                <fieldset class="fieldset">
                    <label class="block">Jenis Kelamin</label>
                    <select wire:model.live="role_id" class="select select-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs">
                        <option value="">-- Pilih --</option>
                        @foreach ($role as $role )
                        <option value="{{ $role->id }}">{{$role->name}}</option>
                        @endforeach
                    </select>
                    <x-label-error :messages="$errors->get('role_id')" />
                </fieldset>
            </div>

            <div class="modal-action">
                <flux:button wire:click="save" size="xs" icon:trailing="save" variant="primary"> {{ $userId ? 'Update' : 'Simpan' }}</flux:button>
                <flux:button size="xs" wire:click="$set('showModal', false)" icon:trailing="circle-x" variant="danger">Batal</flux:button>
            </div>
        </div>
    </dialog>


    {{-- Delete Confirmation Modal --}}
    <dialog class="modal" @if($showDeleteModal) open @endif>
        <div class="modal-box">
            <h3 class="font-bold text-lg">Confirm Delete</h3>
            <p>Are you sure you want to delete this user?</p>
            <div class="modal-action">
                <button class="btn" wire:click="$set('showDeleteModal', false)">Cancel</button>
                <button class="btn btn-error" wire:click="delete">Delete</button>
            </div>
        </div>
    </dialog>
    </div>

    <dialog class="modal" @if($showImportModal) open @endif>
        <div class="modal-box w-11/12 max-w-md">
            <h3 class="font-bold text-lg">Import Users</h3>

            <fieldset class="fieldset">
                <label class="block">File Excel</label>
                <input type="file" wire:model.live="file" class="input input-bordered w-full focus:ring-1 focus:border-info focus:ring-info focus:outline-hidden input-xs" />

                {{-- Error message --}}
                <x-label-error :messages="$errors->get('file')" />

                {{-- Loading indicator saat pilih file --}}
                <div wire:loading wire:target="file" class="text-info text-sm mt-1">
                    ⏳ Sedang mengunggah file...
                </div>
            </fieldset>
            @if (session()->has('success'))
            <div class="alert alert-success my-2">
                {{ session('success') }}
            </div>
            @endif
            <div class="modal-action">
                {{-- Tombol Import --}}
                <flux:button wire:click="import" size="xs" icon:trailing="save" variant="primary" wire:loading.attr="disabled" wire:target="import,file">

                    <span wire:loading.remove wire:target="import,file">Import</span>
                    <span wire:loading wire:target="import,file">Mengimpor...</span>
                </flux:button>

                {{-- Tombol Batal --}}
                <flux:button size="xs" wire:click="$set('showImportModal', false)" icon:trailing="circle-x" variant="danger">
                    Batal
                </flux:button>
            </div>
        </div>
    </dialog>

    <dialog class="modal" @if($showBulkUpdateModal) open @endif>
        <div class="modal-box">
            <h3 class="font-bold text-lg">Bulk Update User</h3>

            <fieldset class="fieldset">
                <label class="block">Role Baru</label>
                <select wire:model="bulkRole" class="select select-bordered w-full input-xs">
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </fieldset>
            <div class="modal-action">
                <flux:button size="xs" wire:click="bulkUpdate" variant="primary">Update</flux:button>
                <flux:button size="xs" wire:click="$set('showBulkUpdateModal', false)" variant="danger">Batal</flux:button>
            </div>
        </div>
    </dialog>
</section>
