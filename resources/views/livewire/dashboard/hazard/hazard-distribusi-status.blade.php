    <div wire:ignore id="chart-container" style="height: 320px;" class="w-full"></div>

    @push('scripts')
    <!-- Load ECharts dari CDN -->
    <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
    <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

    <script>
        // 🧠 Ambil data dari Livewire (JSON string → object JS)
        const chartData = JSON.parse('<?php echo $statusChart ?>');
        const labels = chartData.labels;
        const values = chartData.values;
        // 🔥 Masukkan data Livewire ke format yang ECharts butuh
        const seriesData = labels.map((label, i) => ({
            name: label
            , value: values[i]
        }));
        var dom_pie = document.getElementById('chart-container');
        var myChart = echarts.init(dom_pie);
        var option_pie;
        option = {
            title: {
                text: 'Referer of a Website'
                , subtext: 'Fake Data'
                , left: 'center'
            }
            , tooltip: {
                trigger: 'item'
            }
            , legend: {
                orient: 'vertical'
                , left: 'left'
            }
            , series: [{
                name: 'Access From'
                , type: 'pie'
                , radius: '50%'
                , data: [{
                        value: 1048
                        , name: 'Search Engine'
                    }
                    , {
                        value: 735
                        , name: 'Direct'
                    }
                    , {
                        value: 580
                        , name: 'Email'
                    }
                    , {
                        value: 484
                        , name: 'Union Ads'
                    }
                    , {
                        value: 300
                        , name: 'Video Ads'
                    }
                ]
                , emphasis: {
                    itemStyle: {
                        shadowBlur: 10
                        , shadowOffsetX: 0
                        , shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };

        if (option && typeof option === 'object') {
            myChart.setOption(option);
        }

        window.addEventListener('resize', myChart.resize);

    </script>
