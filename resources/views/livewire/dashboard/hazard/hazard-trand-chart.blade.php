<div x-data="hazardTrendChart(@js($months), @js($counts))" x-init="renderChart()" class="w-full bg-white p-4 rounded-lg shadow">
    <div id="hazardTrend" class="h-64 w-full"></div>
</div>

@script
import * as echarts from 'echarts';

window.hazardTrendChart = (months, counts) => ({
chart: null,
renderChart() {
this.chart = echarts.init(document.getElementById('hazardTrend'));

const option = {
title: {
text: 'Tren Laporan Hazard per Bulan',
left: 'center'
},
tooltip: { trigger: 'axis' },
xAxis: {
type: 'category',
data: months,
axisLine: { lineStyle: { color: '#888' } }
},
yAxis: {
type: 'value',
name: 'Jumlah Laporan'
},
series: [
{
name: 'Laporan',
type: 'line',
data: counts,
smooth: true,
symbol: 'circle',
symbolSize: 8,
lineStyle: { width: 3 },
itemStyle: { color: '#007bff' },
areaStyle: { color: 'rgba(0, 123, 255, 0.2)' }
}
]
};

this.chart.setOption(option);
window.addEventListener('resize', () => this.chart.resize());
}
});
@endscript
