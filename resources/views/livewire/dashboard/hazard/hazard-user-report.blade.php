<div wire:ignore id="container_reportby" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    var dom_reportBy = document.getElementById('container_reportby');
    const pelapor = JSON.parse('<?php echo $pelapor ?>');


    var myChart_reportBy = echarts.init(dom_reportBy, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    // 🎨 Fungsi untuk menghasilkan warna berbeda-beda otomatis
    function generateColor(index, total) {
        // Gunakan lingkaran warna (HSL)
        const hue = (index * (360 / total)) % 360; // bagi rata keliling 360°
        return `hsl(${hue}, 65%, 55%)`; // saturasi & lightness agar tetap cerah
    }

    var option_reportBy;

    option_reportBy = {
        title: {
            text: 'Jumlah Laporan'
        }
        , grid: {
            top: 50
            , left: 180
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
            ,inverse: true // ⬅️ urutkan dari atas ke bawah sesuai urutan data
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
    }

    window.addEventListener('resize', myChart_reportBy.resize);

</script>
@endpush
