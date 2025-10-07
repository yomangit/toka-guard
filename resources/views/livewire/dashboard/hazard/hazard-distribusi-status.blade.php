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
        option_pie = {
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



        if (option_pie && typeof option_pie === 'object') {
            myChart.setOption(option_pie);
        }

        window.addEventListener('resize', myChart.resize);

    </script>
