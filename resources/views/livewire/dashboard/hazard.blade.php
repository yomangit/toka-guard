<section>
    {{-- Header --}}
    <h1 class="text-xl font-bold">Hazard Report Dashboard</h1>
    <p class="text-xs text-gray-600">Ringkasan kondisi laporan hazard terkini</p>
    <x-tabs-dashboard.layout>

        {{-- Statistik Ringkas --}}
        <div class="stats stats-vertical lg:stats-horizontal shadow w-full">

            {{-- Total Laporan --}}
            <div class="stat">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="stat-title">Total Laporan</div>
                <div class="stat-value text-primary">{{ $totalHazard }}</div>
                <div class="stat-desc">Semua laporan hazard</div>
            </div>

            {{-- Sedang Diproses --}}
            <div class="stat">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div class="stat-title">Sedang Diproses</div>
                <div class="stat-value text-secondary">
                    {{ $hazardByStatus['in_progress'] ?? 0 }}
                </div>
                <div class="stat-desc">Laporan aktif</div>
            </div>

            {{-- Submitted --}}
            <div class="stat">
                <div class="stat-figure text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div class="stat-title">Submitted</div>
                <div class="stat-value text-info">
                    {{ $hazardByStatus['submitted'] ?? 0 }}
                </div>
                <div class="stat-desc">Menunggu diproses</div>
            </div>

            {{-- Closed --}}
            <div class="stat">
                <div class="stat-figure text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block h-8 w-8 stroke-current">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="stat-title">Selesai</div>
                <div class="stat-value text-success">
                    {{ $hazardByStatus['closed'] ?? 0 }}
                </div>
                <div class="stat-desc">Laporan selesai</div>
            </div>

        </div>


        {{-- Grafik --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-2 my-2">
            <div class="bg-white rounded-xl lg:col-span-2 shadow">
                <livewire:dashboard.hazard.hazard-trand-chart />
            </div>
            <div class="bg-white p-4 rounded-xl shadow">
                <livewire:dashboard.hazard.hazard-distribusi-status />
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-2 my-2">
            <div class="bg-white rounded-xl shadow">
                <livewire:dashboard.hazard.hazard-distribusi-divisi />
            </div>
            <div class="bg-white p-4 rounded-xl shadow">

            </div>
        </div>

        {{-- Daftar Laporan Terbaru --}}
        <div class="bg-white p-4 rounded-xl shadow">
            <h3 class="font-semibold mb-4">Laporan Hazard Terbaru</h3>
            <table class="table table-xs">
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
                    <td>1</td>
                    <td>2</td>
                    <td>3</td>
                    <td>4</td>
                    <td>5</td>
                </tbody>
            </table>
        </div>
    </x-tabs-dashboard.layout>
</section>
