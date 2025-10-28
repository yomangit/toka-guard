<div>
    <!-- Tombol untuk membuka modal -->
    <x-flux:button icon="upload" wire:click="$set('showModal', true)" class="btn-primary">
        Import Excel
    </x-flux:button>

    <!-- Modal -->
    <x-flux:modal wire:model="showModal" title="Import Data Hazard Reports" size="md">
        <div class="space-y-4">
            <p class="text-sm text-gray-600">
                Unggah file Excel (.xlsx / .csv) sesuai format header hazard report.
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

        <x-slot name="footer">
            <div class="flex justify-end gap-2">
                <x-flux:button variant="ghost" wire:click="$set('showModal', false)">
                    Batal
                </x-flux:button>

                <x-flux:button variant="primary" wire:click="import" wire:loading.attr="disabled">
