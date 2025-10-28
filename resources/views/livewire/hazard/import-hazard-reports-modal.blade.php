<div>
    <!-- Tombol trigger modal -->
    <x-flux.modal.trigger name="importHazardModal">
        <x-flux.button icon="upload" class="btn-primary">
            Import Excel
        </x-flux.button>
    </x-flux.modal.trigger>

    <!-- Modal -->
    <x-flux.modal name="importHazardModal" size="md">
        <x-slot name="title">
            Import Data Hazard Reports
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Unggah file Excel (.xlsx / .csv) sesuai format kolom hazard report.
                </p>

                <div>
                    <input type="file" wire:model="file" accept=".xlsx,.xls,.csv"
                           class="file-input file-input-bordered w-full border-gray-300" />

                    @error('file')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="file" class="text-sm text-blue-500 mt-2">
                        Mengunggah file...
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-flux.modal.close>
                    <x-flux.button flat>Batal</x-flux.button>
                </x-flux.modal.close>

                <x-flux.button wire:click="import" wire:loading.attr="disabled" primary>
                    <span wire:loading.remove>Import</span>
                    <span wire:loading>Memproses...</span>
                </x-flux.button>
            </div>
        </x-slot>
    </x-flux.modal>
</div>
