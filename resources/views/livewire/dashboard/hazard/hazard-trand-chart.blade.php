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
            textStyle: {
                fontFamily: 'Microsoft YaHei'
                , fontSize: 12
                , fontStyle: 'normal'
                , fontWeight: 'normal'
            , }
            , xAxis: {
                type: 'category'
                , data: data.months
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                    , fontStyle: 'normal'
                    , fontWeight: 'normal'
                , }
            }
            , yAxis: {
                type: 'value'
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                    , fontStyle: 'normal'
                    , fontWeight: 'normal'
                , }
            }
            , series: [{
                data: data.counts
                , type: 'line'
                , smooth: true
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }

        window.addEventListener('resize', myChart.resize);

    </script>
    @endpush
