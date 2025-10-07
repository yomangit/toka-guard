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
            , grid: {
                top: 50
                , right: 30
                , bottom: 50
                , left: 50
                , containLabel: true
            }
            , tooltip: {
                trigger: 'axis'
                , backgroundColor: 'rgba(50,50,50,0.8)'
                , borderWidth: 0
                , textStyle: {
                    color: '#fff'
                    , fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                , }
            }
            , legend: {
                data: ['Jumlah Laporan']
                , top: 10
                , left: 'center'
                , textStyle: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                    , fontWeight: 'normal'
                }
            }
            , xAxis: {
                type: 'category'
                , data: data.months
                , axisLine: {
                    lineStyle: {
                        color: '#888'
                    }
                }
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                }
                , axisTick: {
                    show: false
                }
            }
            , yAxis: {
                type: 'value'
                , axisLine: {
                    lineStyle: {
                        color: '#888'
                    }
                }
                , splitLine: {
                    lineStyle: {
                        type: 'dashed'
                        , color: '#ddd'
                    }
                }
                , axisLabel: {
                    fontFamily: 'Microsoft YaHei'
                    , fontSize: 12
                }
            }
            , series: [{
                name: 'Jumlah Laporan'
                , data: data.counts
                , type: 'line'
                , smooth: true
                , lineStyle: {
                    width: 3
                }
                , symbol: 'circle'
                , symbolSize: 6
                , itemStyle: {
                    color: '#3B82F6'
                }
                , areaStyle: {
                    opacity: 0.1
                    , color: '#3B82F6'
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }


        window.addEventListener('resize', myChart.resize);

    </script>
    @endpush
