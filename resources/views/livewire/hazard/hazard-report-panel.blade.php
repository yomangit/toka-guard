<div class="space-y-4">
    @if (session()->has('message'))
    <div class="bg-green-100 text-green-700 p-2 rounded">
        {{ session('message') }}
    </div>
    @endif

    <div class="overflow-auto ">
        <table class="table table-xs border text-sm px-2">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">Judul</th>
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
                    <td class="border px-2 py-1">{{ $report->title ?? '-' }}</td>
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
                        @if(
                        auth()->user()->hasRole('administrator') ||
                        auth()->id() === $report->pelapor_id ||
                        auth()->id() === $report->penanggung_jawab_id
                        )
                        <a href="{{ route('hazard-detail', $report->id) }}" class="text-blue-600 text-sm hover:underline">
                            Detail
                        </a>
                        @else
                        <span class="text-gray-400 text-sm cursor-not-allowed">Detail</span>
                        @endif
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
</div>
