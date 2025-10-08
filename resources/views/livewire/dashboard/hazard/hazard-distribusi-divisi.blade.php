<div wire:ignore id="container" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    var dom = document.getElementById('container');
     const data = JSON.parse('<?php echo $categories ?>');
     console.log(data);
     
    var myChart = echarts.init(dom, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    var app = {};

    var option;

    option = {
        title: {
            text: 'World Population'
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
            , data: data.label
        }
        , series: [{
            name: '2011'
            , type: 'bar'
            , data: data.counts
        }]
    };

    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

</script>
@endpush
