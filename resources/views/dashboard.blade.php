<x-layouts.app :title="__('Dashboard')">
    <div class="p-6 space-y-6">

        {{-- Header --}}
        <h1 class="text-2xl font-bold">Hazard Report Dashboard</h1>
        <p class="text-gray-600">Ringkasan kondisi laporan hazard terkini</p>

        {{-- Statistik Ringkas --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow">
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <div class="stat-title">Total Likes</div>
                <div class="stat-value text-primary">25.6K</div>
                <div class="stat-desc">21% more than last month</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="stat-title">Page Views</div>
                <div class="stat-value text-secondary">2.6M</div>
                <div class="stat-desc">21% more than last month</div>
            </div>

            <div class="stat">
                <div class="stat-figure text-secondary">
                    <div class="avatar avatar-online">
                        <div class="w-16 rounded-full">
                            <img src="https://img.daisyui.com/images/profile/demo/anakeen@192.webp" />
                        </div>
                    </div>
                </div>
                <div class="stat-value">86%</div>
                <div class="stat-title">Tasks done</div>
                <div class="stat-desc text-secondary">31 tasks remaining</div>
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
            chart: {
                type: 'line'
                , height: 250
            }
            , series: [{
                name: 'Laporan'
                , data: @json($trendData ? ? [])
            }]
            , xaxis: {
                categories: @json($trendLabels ? ? [])
            }
        };
        new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

        var statusOptions = {
            chart: {
                type: 'donut'
                , height: 250
            }
            , series: @json($statusSeries ? ? [])
            , labels: @json($statusLabels ? ? [])
        };
        new ApexCharts(document.querySelector("#statusChart"), statusOptions).render();

    </script>
    @endpush
</x-layouts.app>
