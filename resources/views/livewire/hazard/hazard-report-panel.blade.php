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
        <div class="overflow-auto ">
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
    </x-manhours.layout>
    {{ $reports->links() }}
</section>
