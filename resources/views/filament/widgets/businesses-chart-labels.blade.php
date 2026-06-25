<script>
(function () {
    window.filamentChartJsPlugins = window.filamentChartJsPlugins ?? [];

    if (!window.filamentChartJsPlugins.some(function (p) { return p.id === 'barLabels-businesses'; })) {
        window.filamentChartJsPlugins.push({
            id: 'barLabels-businesses',

            beforeUpdate: function (chart) {
                if ((chart.data.datasets[0] || {}).chartId !== 'businesses-per-year') return;
                var isDark = document.documentElement.classList.contains('dark');
                var textColor = isDark ? 'rgba(255,255,255,0.75)' : 'rgba(55,65,81,0.9)';

                if (chart.options.scales) {
                    Object.values(chart.options.scales).forEach(function (scale) {
                        if (scale.ticks) scale.ticks.color = textColor;
                    });
                }
                if (chart.options.plugins && chart.options.plugins.legend && chart.options.plugins.legend.labels) {
                    chart.options.plugins.legend.labels.color = textColor;
                }
            },

            afterDraw: function (chart) {
                if ((chart.data.datasets[0] || {}).chartId !== 'businesses-per-year') return;

                var isDark = document.documentElement.classList.contains('dark');
                var labelColor = isDark ? 'rgba(255,255,255,0.9)' : 'rgba(30,41,59,0.9)';
                var ctx = chart.ctx;
                var labels = chart.data.labels || [];

                labels.forEach(function (label, j) {
                    var total = 0;
                    var minY = Infinity;

                    chart.data.datasets.forEach(function (dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        if (meta.hidden) return;
                        var bar = meta.data[j];
                        if (!bar) return;
                        total += (dataset.data[j] || 0);
                        if (bar.y < minY) minY = bar.y;
                    });

                    if (!total || minY === Infinity) return;

                    ctx.save();
                    ctx.font = 'bold 13px ui-sans-serif, system-ui, sans-serif';
                    ctx.fillStyle = labelColor;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';
                    var x = chart.getDatasetMeta(0).data[j].x;
                    ctx.fillText(total, x, minY - 4);
                    ctx.restore();
                });
            },
        });
    }
})();
</script>
