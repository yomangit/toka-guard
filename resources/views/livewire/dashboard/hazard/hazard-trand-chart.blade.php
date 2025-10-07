<div class="w-full bg-white p-4 rounded-lg shadow">
    <div wire:ignore id="hazardTrend" style="height: 400px;" class="w-full"></div>
</div>

@push('scripts')
<!-- Load ECharts dari CDN -->
<script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
<script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

<script>
    const data = JSON.parse('<?php echo $data ?>');
    console.log(data);
    
    const counts = @json($counts);
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
            , data: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
        }
        , yAxis: {
            type: 'value'
        }
        , series: [{
            data: [150, 230, 224, 218, 135, 147, 260]
            , type: 'line'
        }]
    };



    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);

</script>
@endpush
