<canvas id="inquiryChart"></canvas>
<script>
    const chart = new Chart(document.getElementById('inquiryChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyCounts->keys()) !!},
            datasets: [{
                label: 'Inquiries',
                data: {!! json_encode($monthlyCounts->values()) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
