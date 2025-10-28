<div>
    <!-- Tombol Buka Modal -->
    <x-button wire:click="openModal" icon="upload" class="btn-primary">
        Import Excel
    </x-button>

    <!-- Modal Upload -->
    <x-modal wire:model="open" size="md">
        <x-slot name="title">
            Import Data Hazard Reports
        </x-slot>

        <x-slot name="content">
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Unggah file Excel (.xlsx / .csv) sesuai format header hazard report.
                </p>

                <div>
                    <input type="file" wire:model="file" accept=".xlsx,.xls,.csv"
                        class="file-input file-input-bordered w-full" />

                    @error('file')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror

                    <div wire:loading wire:target="file" class="text-sm text-info mt-2">
                        Mengunggah file...
                    </div>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-button flat wire:click="closeModal" wire:loading.attr="disabled">Batal</x-button>

            <x-button primary wire:click="import" wire:loading.attr="disabled">
                <span wire:loading.remove>Import</span>
                <span wire:loading>Memproses...</span>
            </x-button>
        </x-slot>
    </x-modal>
</div>
