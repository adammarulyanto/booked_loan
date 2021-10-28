    <body class="bg-light">
<!-- Page content-->
<div class="cont-dashboard container-fluid">
    <h1 class="mt-4">Dashboard</h1>
    <div class="row">
        <div class="col-lg-9 col-12 box-dashboard">
            <div class="col-12 box-dashboard-inside">
                <h4>Claimed</h4>
                <script src="https://cdn.jsdelivr.net/npm/chart.js@3.4.1/dist/chart.min.js"></script>
                <canvas id="myChart" class="canvas-cart"></canvas>
                <script>
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: [<?php foreach($booked_loan as $bulan){echo '"'.$bulan->bulan.'",';}?>],
                        datasets: [{
                            label: 'Booked Amount (Rp)',
                            data: [<?php foreach($booked_loan as $claimed){echo '"'.$claimed->amount.'",';}?>],
                            backgroundColor: [
                                <?php foreach($booked_loan as $warna){echo "'rgba(54, 162, 235, 0.2)',";}?>
                            ],
                            borderColor: [
                                <?php foreach($booked_loan as $border){echo "'rgba(54, 162, 235, 0.2)',";}?>
                            ],
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
            </div>
        </div>
        <div class="col-lg-3 col-12 box-dashboard">
            <div class="col-12 box-dashboard-inside">
                <h4>Product</h4>
                <canvas id="myChart2" width="400" height="400"></canvas>
                <script>
                var ctx = document.getElementById('myChart2').getContext('2d');
                var myChart2 = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [<?php foreach($product as $namaprod){echo '"'.$namaprod->product.'",';}?>],
                      datasets: [{
                        label: 'My First Dataset',
                        data: [<?php foreach($product as $amountprod){echo '"'.$amountprod->amount.'",';}?>],
                        backgroundColor: [
                          'rgb(234, 234, 87)',
                          'rgb(87, 234, 160)',
                          'rgb(87, 160, 234)',
                          'rgb(160, 87, 234)',
                          'rgb(234, 87, 160)',
                          'rgb(234, 160, 87)',
                          'rgb(160, 234, 87)',
                          'rgb(12, 58, 104)',
                          'rgb(87, 160, 234)',
                          'rgb(58, 104, 12)'
                        ],
                        hoverOffset: 4
                      }]
                    },
                    options: {
                    }
                });
                </script>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.getElementById("menu-dashboard").className = "list-group-item list-group-item-action p-3 menu-active";
</script>
</body>