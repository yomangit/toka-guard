    <div wire:ignore id="chart-container" style="height: 320px;" class="w-full"></div>

    @push('scripts')
    <!-- Load ECharts dari CDN -->
    <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
    <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

    <script>
        document.addEventListener('livewire:load', () => {
            var dom = document.getElementById('chart-container');
            var myChart = echarts.init(dom, null, {
                renderer: 'canvas'
                , useDirtyRect: false
            });

            // 🧠 Ambil data dari Livewire (JSON string → object JS)
            const chartData = JSON.parse(@this.statusChart);
            const labels = chartData.labels;
            const values = chartData.values;

            // 🔥 Masukkan data Livewire ke format yang ECharts butuh
            const seriesData = labels.map((label, i) => ({
                name: label
                , value: values[i]
            }));

            var option = {
                title: {
                    text: 'Distribusi Berdasarkan Status'
                    , subtext: 'Data laporan hazard'
                    , left: 'center'
                }
                , tooltip: {
                    trigger: 'item'
                    , formatter: '{b}: {c} laporan ({d}%)'
                }
                , legend: {
                    orient: 'vertical'
                    , left: 'left'
                }
                , series: [{
                    name: 'Status'
                    , type: 'pie'
                    , radius: '50%'
                    , data: seriesData
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

            window.addEventListener('resize', () => myChart.resize());
        });
