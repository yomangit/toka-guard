    <div wire:ignore id="chart-container" style="height: 320px;" class="w-full"></div>

    @push('scripts')
    <!-- Load ECharts dari CDN -->
    <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
    <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

    <script>
        var dom = document.getElementById('chart-container');
        var myChart = echarts.init(dom, null, {
            renderer: 'canvas'
            , useDirtyRect: false
        })

        // 🧠 Ambil data dari Livewire (JSON string → object JS)
        const chartData = JSON.parse('<?php echo $statusChart ?>');
        const labels = chartData.labels;
        const values = chartData.values;

        // 🔥 Masukkan data Livewire ke format yang ECharts butuh
        const seriesData = labels.map((label, i) => ({
            name: label
            , value: values[i]
        }));
        console.log(seriesData);

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
