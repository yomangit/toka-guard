<!-- resources/views/livewire/hazard-list.blade.php -->
<section class="w-full">
    
    <x-toast />
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
  
    @include('partials.header-hazard')
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
    <x-manhours.layout>
        <livewire:hazard.hazard-report-panel />
    </x-manhours.layout>

</section>
