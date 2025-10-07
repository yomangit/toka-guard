    <div wire:ignore id="hazardTrend" style="height: 250px;" class="w-full"></div>
    @push('scripts')
    <!-- Load ECharts dari CDN -->
    <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
    <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

    <script>
        const data = JSON.parse('<?php echo $data ?>');
        console.log(data);
        var dom = document.getElementById('hazardTrend');
        var myChart = echarts.init(dom);
        var option;

        option = {
            xAxis: {
                type: 'category'
                , data: data.months
            }
            , yAxis: {
                type: 'value'
            }
            , series: [{
                data: data.counts
                , type: 'line'
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }

        window.addEventListener('resize', myChart.resize);

    </script>
    @endpush
