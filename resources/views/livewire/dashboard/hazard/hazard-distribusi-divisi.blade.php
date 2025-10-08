<div wire:ignore id="container" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    var dom_divis = document.getElementById('container');
    const categories = JSON.parse('<?php echo $categories ?>');
    console.log(categories);

    var myChart_divis = echarts.init(dom_divis, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    var app = {};

    var option_divis;

    option_divis = {
        title: {
            text: 'Distribusi Berdasarkan Departemen/Kontraktor'
            , left: 'center'
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
            , formatter: function(params) {
                const name = params[0].name;
                const value = params[0].value;
                return `<b>${name}</b><br/>Jumlah: ${value}`;
            }
        }
        , 
        , legend: {}
        , xAxis: {
            type: 'value'
            , boundaryGap: [0, 0.01]
        }
        , yAxis: {
            type: 'category'
            , data: fixedLabels
            , axisLabel: {
                interval: 0, // tampilkan semua label
                formatter: function(value) {
                    return value.length > 20 ? value.substring(0, 20) + '…' : value;
                }
            }
        }
        , series: [{
            name: '2011'
            , type: 'bar'
            , data: categories.counts
        }]
    };

    if (option_divis && typeof option_divis === 'object') {
        myChart_divis.setOption(option_divis);
    }

    window.addEventListener('resize', myChart_divis.resize);

</script>
@endpush
