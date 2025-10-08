<div wire:ignore id="container" style="height: 320px"></div>
@push('scripts')
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script type="text/javascript">
    var dom = document.getElementById('container');
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
            , data: ['Brazil', 'Indonesia', 'USA', 'India', 'China', 'World']
        }
        , series: [{
            name: '2011'
            , type: 'bar'
            , data: [18203, 23489, 29034, 104970, 131744, 630230]
        }]
    };

    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

</script>
@endpush
