<span class="label label-info">{{ $title }}</span>
<canvas id="myChart{{ $id }}" width="250" height="250"></canvas>
<script>
$(function () {
    var ctx = document.getElementById("myChart{{ $id }}").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [{!! $layer !!}],
            datasets: [{
                label: "{{ $legend }}",
                data: [{{ $data }}],
               backgroundColor: [
                   {!! $colors !!}
               ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }
        }
    });
});
</script>