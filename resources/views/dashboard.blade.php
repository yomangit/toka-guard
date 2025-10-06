<x-layouts.app :title="__('Dashboard')">
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <h1 class="text-2xl font-bold">Hazard Report Dashboard</h1>
        <p class="text-gray-600">Ringkasan kondisi laporan hazard terkini</p>

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-sm text-gray-500">Total Laporan</p>
                <h2 class="text-2xl font-bold">{{ $totalHazards ?? 0 }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-sm text-gray-500">Sedang Diproses</p>
                <h2 class="text-2xl font-bold text-blue-600">{{ $inProgress ?? 0 }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-sm text-gray-500">Selesai (Closed)</p>
                <h2 class="text-2xl font-bold text-green-600">{{ $closed ?? 0 }}</h2>
            </div>
            <div class="bg-white p-4 rounded-xl shadow">
                <p class="text-sm text-gray-500">Overdue</p>
                <h2 class="text-2xl font-bold text-red-600">{{ $overdue ?? 0 }}</h2>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-semibold mb-2">Trend Laporan per Bulan</h3>
                <div id="trendChart" class="h-64"></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow">
                <h3 class="font-semibold mb-2">Distribusi Berdasarkan Status</h3>
                <div id="statusChart" class="h-64"></div>
            </div>
        </div>

        {{-- Daftar Laporan Terbaru --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-4">Laporan Hazard Terbaru</h3>
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">ID</th>
                        <th class="px-3 py-2 border">Judul</th>
                        <th class="px-3 py-2 border">Status</th>
                        <th class="px-3 py-2 border">Pelapor</th>
                        <th class="px-3 py-2 border">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @forelse($latestHazards as $hazard)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $hazard->id }}</td>
                            <td class="px-3 py-2 border">{{ $hazard->title }}</td>
                            <td class="px-3 py-2 border">
                                <span class="px-2 py-1 text-xs rounded bg-gray-200">
                                    {{ $hazard->status }}
                                </span>
                            </td>
                            <td class="px-3 py-2 border">{{ $hazard->pelapor->name ?? '-' }}</td>
                            <td class="px-3 py-2 border">{{ $hazard->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">Belum ada laporan</td>
                        </tr>
                    @endforelse --}}
                </tbody>
            </table>
        </div>

    </div>

    {{-- Tambahkan ChartJS / ApexCharts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            var trendOptions = {
                chart: { type: 'line', height: 250 },
                series: [{
                    name: 'Laporan',
                    data: @json($trendData ?? [])
                }],
                xaxis: { categories: @json($trendLabels ?? []) }
            };
            new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

            var statusOptions = {
                chart: { type: 'donut', height: 250 },
                series: @json($statusSeries ?? []),
                labels: @json($statusLabels ?? [])
            };
            new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();
        </script>
    @endpush
</x-layouts.app>
