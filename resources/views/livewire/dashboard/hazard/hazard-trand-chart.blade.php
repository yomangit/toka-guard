<div x-data="hazardTrendChart(@js($months), @js($counts))" x-init="renderChart()" class="w-full bg-white p-4 rounded-lg shadow">
    <div wire:ignore id="hazardTrend" style="height: 400px;" class="w-full"></div>
</div>

@push('scripts')
<!-- Load ECharts dari CDN -->
<script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
<script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

<script>
        const chartDom = document.getElementById('hazardTrend');
        var myChart = echarts.init(chartDom);
        const months = @json($months);
        const counts = @json($counts);
        console.log(months);

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

        option && myChart.setOption(option);
        window.addEventListener('resize', () => chart.resize());
</script>
@endpush
