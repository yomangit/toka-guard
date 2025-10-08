<div wire:ignore id="container" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    var dom_divis = document.getElementById('container');
    const categories = JSON.parse('<?php echo $categories ?>');


    var myChart_divis = echarts.init(dom_divis, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    // 🎨 Fungsi untuk menghasilkan warna berbeda-beda otomatis
    function generateColor(index, total) {
        // Gunakan lingkaran warna (HSL)
        const hue = (index * (360 / total)) % 360; // bagi rata keliling 360°
        return `hsl(${hue}, 65%, 55%)`; // saturasi & lightness agar tetap cerah
    }

    var option_divis;

    option_divis = {
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
            , data: categories.label
        }
        , series: [{
            name: categories.year, // ✅ ambil dari data Livewire
            , type: 'bar'
            , data: categories.counts
            , itemStyle: {
                color: function(params) {
                    // Gunakan warna dinamis berdasarkan posisi bar
                    return generateColor(params.dataIndex, categories.counts.length);
                }
                , borderRadius: [0, 6, 6, 0]
            }
        }]
    };

    if (option_divis && typeof option_divis === 'object') {
        myChart_divis.setOption(option_divis);
    }

    window.addEventListener('resize', myChart_divis.resize);

</script>
@endpush
