<div class="w-full bg-white p-4 rounded-lg shadow">
    <div wire:ignore id="hazardTrend" style="height: 400px;" class="w-full"></div>
</div>

@push('scripts')
<!-- Load ECharts dari CDN -->
<script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
<script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

<script>
    const months = JSON.parse('<?php echo $months ?>');
    const counts =JSON.parse('<?php echo $counts ?>');
    var dom = document.getElementById('hazardTrend');
    var myChart = echarts.init(dom, null, {
        renderer: 'canvas'
        , useDirtyRect: false
    });
    var app = {};


    var option;

    option = {
        xAxis: {
            type: 'category'
            , data: months,
        }
        , yAxis: {
            type: 'value'
        }
        , series: [{
             data: counts,
            , type: 'line'
        }]
    };



    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

</script>
@endpush
