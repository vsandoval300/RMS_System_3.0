<script>
(function () {
    window.filamentChartJsPlugins = window.filamentChartJsPlugins ?? [];
    if (!window.filamentChartJsPlugins.some(function (p) { return p.id === 'barLabels-reinsurers'; })) {
        window.filamentChartJsPlugins.push({
            id: 'barLabels-reinsurers',
            afterDraw: function (chart) {
                if ((chart.data.datasets[0] || {}).chartId !== 'reinsurers-per-year') return;

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
                    ctx.fillStyle = 'rgba(255,255,255,0.9)';
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
