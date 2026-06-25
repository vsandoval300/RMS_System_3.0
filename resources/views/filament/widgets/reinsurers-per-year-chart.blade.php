<script>
    (function () {
        window.filamentChartJsPlugins = window.filamentChartJsPlugins ?? [];
        if (!window.filamentChartJsPlugins.some(function (p) { return p.id === 'barLabels-reinsurers'; })) {
            window.filamentChartJsPlugins.push({
                id: 'barLabels-reinsurers',
                afterDatasetsDraw: function (chart) {
                    if ((chart.data.datasets[0] || {}).label !== 'Reinsurers') return;
                    var ctx = chart.ctx;
                    chart.data.datasets.forEach(function (dataset, i) {
                        chart.getDatasetMeta(i).data.forEach(function (bar, j) {
                            var value = dataset.data[j];
                            if (!value) return;
                            ctx.save();
                            ctx.font = 'bold 11px ui-sans-serif, system-ui, sans-serif';
                            ctx.fillStyle = '#41A2C3';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'bottom';
                            ctx.fillText(value, bar.x, bar.y - 3);
                            ctx.restore();
                        });
                    });
                },
            });
        }
    })();
</script>

@include('filament-widgets::chart-widget')
