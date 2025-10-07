<div x-data="hazardTrendChart(@js($months), @js($counts))" x-init="renderChart()" class="w-full bg-white p-4 rounded-lg shadow">
    <div wire:ignore id="hazardTrend" style="height: 400px;" class="w-full"></div>
</div>

@push('scripts')
<!-- Load ECharts dari CDN -->
  <script src="https://echarts.apache.org/en/js/vendors/echarts/dist/echarts.min.js"></script>
   <script src="https://echarts.apache.org/en/js/vendors/echarts-gl/dist/echarts-gl.min.js"></script>

<script>
    document.addEventListener('livewire:load', () => {
        renderHazardTrendChart();
    });

    document.addEventListener('livewire:update', () => {
        renderHazardTrendChart();
    });

    function renderHazardTrendChart() {
        const chartDom = document.getElementById('hazardTrend');
        var chart = echarts.init(chartDom);
        const months = JSON.parse('<?php echo $months ?>');
        const counts =JSON.parse('<?php echo $counts ?>');
        console.log(months);
        
        const option = {
            title: {
                text: 'Tren Laporan Hazard per Bulan'
                , left: 'center'
            }
            , tooltip: {
                trigger: 'axis'
            }
            , xAxis: {
                type: 'category'
                , data: months
                , axisLine: {
                    lineStyle: {
                        color: '#888'
                    }
                }
            }
            , yAxis: {
                type: 'value'
                , name: 'Jumlah Laporan'
            }
            , series: [{
                name: 'Laporan'
                , type: 'line'
                , data: counts
                , smooth: true
                , symbol: 'circle'
                , symbolSize: 8
                , lineStyle: {
                    width: 3
                }
                , itemStyle: {
                    color: '#007bff'
                }
                , areaStyle: {
                    color: 'rgba(0, 123, 255, 0.2)'
                }
            }]
        };

        chart.setOption(option);
        window.addEventListener('resize', () => chart.resize());
    }

</script>
@endpush
