    <div wire:ignore id="chart-container" style="height: 320px;" class="w-full"></div>
    @push('scripts')
    <!-- Load ECharts dari CDN -->
    <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
    <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>
    <script>
        setInterval(() => Livewire.dispatch('chartUpdated'), 1000);
        var dom_status = document.getElementById('chart-container');
        // 🧠 Ambil data dari Livewire (JSON string → object JS)
        const chartData = JSON.parse('<?php echo $statusChart ?>');
        const labels = chartData.labels;
        const values = chartData.values;
        // 🔥 Masukkan data Livewire ke format yang ECharts butuh
        const seriesData = labels.map((label, i) => ({
            name: label
            , value: values[i]
        }));
        var myChart_status = echarts.init(dom_status, null, {
            renderer: 'canvas'
            , useDirtyRect: false
        });
        var app = {};
        var option_status;

        option_status = {
            title: {
                text: 'Distribusi Status'
                , left: 'center'
            }
            , legend: {
                top: 'bottom'
            }
            , toolbox: {
                show: true
                , feature: {
                    mark: {
                        show: true
                    }
                    , dataView: {
                        show: true
                        , readOnly: false
                    }
                    , restore: {
                        show: true
                    }
                    , saveAsImage: {
                        show: true
                    }
                }
            }
            , series: [{
                name: 'Nightingale Chart'
                , type: 'pie'
                , radius: [50, 250]
                , center: ['50%', '50%']
                , roseType: 'area'
                , itemStyle: {
                    borderRadius: 8
                }
                , data: seriesData
            }]
        };


        if (option_status && typeof option_status === 'object') {
            myChart_status.setOption(option_status);
            Livewire.on('distribusiStatus', event => {
                let payload_status = JSON.parse(event); // ini parse JSON dari PHP
                const labels = payload_status.labels;
                const values = payload_status.values;

                // Bentuk ulang data untuk series chart
                const seriesData = labels.map((label, i) => ({
                    name: label
                    , value: values[i]
                }));

                myChart_status.setOption({
                    series: [{
                        name: 'Nightingale Chart'
                        , type: 'pie'
                        , radius: [50, 250]
                        , center: ['50%', '50%']
                        , roseType: 'area'
                        , itemStyle: {
                            borderRadius: 8
                        }
                        , data: seriesData
                    }]
                });
            });
        }
        window.addEventListener('resize', myChart_status.resize);

    </script>
    @endpush
