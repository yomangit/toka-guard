<div wire:ignore id="container_reportby" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    setInterval(() => Livewire.dispatch('datePelaporUpdated'), 1000);
    var dom_reportBy = document.getElementById('container_reportby');
    const pelapor = JSON.parse('<?php echo $pelapor ?>');


    var myChart_reportBy = echarts.init(dom_reportBy, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    // 🎨 Fungsi untuk menghasilkan warna berbeda-beda otomatis
    function generateColor(index, total) {
        const seed = Math.sin(index + 1) * 10000;
        const hue = (seed - Math.floor(seed)) * 360;
        return `hsl(${hue}, 70%, 55%)`;
    }

    var option_reportBy;

    option_reportBy = {
        title: {
            text: 'Top Kontributor'
        }
        , grid: {
            top: 50
            , left: 110
            , right: 30
            , bottom: 60
        }
        , tooltip: {
            trigger: 'axis'
            , axisPointer: {
                type: 'shadow'
            }
        }
        , legend: {}
        , xAxis: {
            type: 'value'
            , boundaryGap: [0, 0.01]
        }
        , yAxis: {
            type: 'category'
            , data: pelapor.label
            , inverse: true
            , axisLabel: {
                color: '#333', // warna teks
                fontSize: 9, // ukuran font
                fontWeight: 'bold', // ketebalan font (normal | bold | bolder | lighter)
                fontFamily: 'Poppins, sans-serif', // jenis font
                overflow: 'truncate', // potong teks jika terlalu panjang
                width: 150, // batas lebar teks (bisa disesuaikan)
                align: 'right' // posisi teks relatif ke sumbu
            }, // ⬅️ urutkan dari atas ke bawah sesuai urutan data
        }
        , series: [{
            name: pelapor.year // ✅ ambil dari data Livewire
            , type: 'bar'
            , data: pelapor.counts
            , itemStyle: {
                color: function(params) {
                    // Gunakan warna dinamis berdasarkan posisi bar
                    return generateColor(params.dataIndex, pelapor.counts.length);
                }
                , borderRadius: [0, 6, 6, 0]
            }
        }]
    };

    if (option_reportBy && typeof option_reportBy === 'object') {
        myChart_reportBy.setOption(option_reportBy);
        Livewire.on('distribusiPelapor', event => {
            const payload_pelapor = JSON.parse(event);

            // Bentuk ulang warna berdasarkan jumlah bar baru
            const seriesData = payload_pelapor.counts.map((count, index) => ({
                value: count
                , itemStyle: {
                    color: generateColor(index, payload_pelapor.counts.length)
                }
            }));

            // Update chart tanpa re-init
            myChart_reportBy.setOption({
                title: {
                    text: 'Top Kontributor ' + payload_pelapor.year
                }
                , yAxis: {
                    data: payload_pelapor.label
                    , inverse: true // biar tetap urut dari atas ke bawah
                }
                , series: [{
                    name: payload_pelapor.year
                    , data: payload_pelapor.counts
                    , itemStyle: {
                        color: function(params) {
                            return generateColor(params.dataIndex, payload_pelapor.counts.length);
                        }
                        , borderRadius: [0, 6, 6, 0]
                    }
                }]
            });
        });
    }

    window.addEventListener('resize', myChart_reportBy.resize);

</script>
@endpush
