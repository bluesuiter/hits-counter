<div class="post_hit_record">
    <h1 class="wp-heading-inline">Post Hit Counts</h1>
    <div class="">
        <canvas id="phc_chart"></canvas>
    </div>
</div>

<script src="<?= plugin_dir_url(__DIR__) ?>assets/chart-js.js"></script>
<script>
    const ctx = document.getElementById('phc_chart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($result, 'hit_date')) ?>,
            datasets: [{
                label: '# of hit counts',
                data: <?= json_encode(array_column($result, 'hit_count')) ?>,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>