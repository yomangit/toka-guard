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
            , data: categories.label
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
