<?php
    require "connect.php";

    if (isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['role']) && $_SESSION['role'] == 'ADMIN') {
        $currentYear = date('Y');
        $currentMonth = date('n');

        $startYear = $_GET['startYear'] ?? $currentYear - 5;
        $endYear = $_GET['endYear'] ?? $currentYear;

        if ($startYear > $endYear) {
            $startYear = $endYear - 1;
        }

        if ($startYear >= $endYear) {
            $startYear = $endYear - 1;
            if ($startYear < $currentYear - 10) {
                $startYear = $currentYear - 10;
            }
        }

        $monthYear = $_GET['monthYear'] ?? $currentYear;
        $dailyMonth = $_GET['dayMonth'] ?? $currentMonth;
        $dailyYear = $_GET['dayYear'] ?? $currentYear;

        // Pertahun
        $yearData = [];
        $deliveryPerYear = [];
        $qtyBoxPerYear = [];
        for ($y = $startYear; $y <= $endYear; $y++) {
            $query = $con->query("SELECT SUM(total_price) as total FROM tbl_sales WHERE YEAR(input_at) = $y");
            $result = $query->fetch_assoc();
            $yearData[$y] = $result['total'] ?? 0;

            $qDelivery = $con->query("SELECT COUNT(*) as total FROM tbl_delivery WHERE YEAR(schedule_date) = $y");
            $rDelivery = $qDelivery->fetch_assoc();
            $deliveryPerYear[$y] = $rDelivery['total'] ?? 0;

            $qBox = $con->query("SELECT SUM(total_qty_box) as total FROM tbl_sales WHERE YEAR(input_at) = $y");
            $rBox = $qBox->fetch_assoc();
            $qtyBoxPerYear[$y] = $rBox['total'] ?? 0;
        }

        // Perbulan
        $monthData = [];
        $deliveryPerMonth = [];
        $qtyBoxPerMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $query = $con->query("SELECT SUM(total_price) as total FROM tbl_sales WHERE YEAR(input_at) = $monthYear AND MONTH(input_at) = $m");
            $result = $query->fetch_assoc();
            $monthData[] = $result['total'] ?? 0;

            $qDelivery = $con->query("SELECT COUNT(*) as total FROM tbl_delivery WHERE YEAR(schedule_date) = $monthYear AND MONTH(schedule_date) = $m");
            $rDelivery = $qDelivery->fetch_assoc();
            $deliveryPerMonth[] = $rDelivery['total'] ?? 0;

            $qBox = $con->query("SELECT SUM(total_qty_box) as total FROM tbl_sales WHERE YEAR(input_at) = $monthYear AND MONTH(input_at) = $m");
            $rBox = $qBox->fetch_assoc();
            $qtyBoxPerMonth[] = $rBox['total'] ?? 0;
        }

        // Perhari
        $dayData = [];
        $deliveryPerDay = [];
        $qtyBoxPerDay = [];
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $dailyMonth, $dailyYear);
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $query = $con->query("SELECT SUM(total_price) as total FROM tbl_sales WHERE YEAR(input_at) = $dailyYear AND MONTH(input_at) = $dailyMonth AND DAY(input_at) = $d");
            $result = $query->fetch_assoc();
            $dayData[] = $result['total'] ?? 0;

            $qDelivery = $con->query("SELECT COUNT(*) as total FROM tbl_delivery WHERE YEAR(schedule_date) = $dailyYear AND MONTH(schedule_date) = $dailyMonth AND DAY(schedule_date) = $d");
            $rDelivery = $qDelivery->fetch_assoc();
            $deliveryPerDay[] = $rDelivery['total'] ?? 0;

            $qBox = $con->query("SELECT SUM(total_qty_box) as total FROM tbl_sales WHERE YEAR(input_at) = $dailyYear AND MONTH(input_at) = $dailyMonth AND DAY(input_at) = $d");
            $rBox = $qBox->fetch_assoc();
            $qtyBoxPerDay[] = $rBox['total'] ?? 0;
        }
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <?php
            include "components/beforeLoad.php";
        ?>
    </head>
    <body>
        <?php
            include "components/navigation.php";
        ?>

        <main>
            <section class="container section section__height">
                <h2 class="section__title">Beranda</h2>
                <form method="GET" style="width: 100%;">
                    <!-- PERTAHUN -->
                    <div class="box-chart">
                        <h3>Data Pertahun</h3>

                        <div class="chart-control">
                            <div>
                                <p>Dari:</p>
                                <div class="input-control">
                                    <button type="button" onclick="changeValue('startYear', -1)">-</button>
                                    <span id="startYearDisplay"><?= $startYear ?></span>
                                    <button type="button" onclick="changeValue('startYear', 1)">+</button>
                                </div>
                            </div>
                            <div>
                                <p>Sampai:</p>
                                <div class="input-control">
                                    <button type="button" onclick="changeValue('endYear', -1)">-</button>
                                    <span id="endYearDisplay"><?= $endYear ?></span>
                                    <button type="button" onclick="changeValue('endYear', 1)">+</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="startYear" id="startYear" value="<?= $startYear ?>">
                        <input type="hidden" name="endYear" id="endYear" value="<?= $endYear ?>">

                        <div class="btn-control">
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=dlv_year_excel&start=<?=$startYear?>&end=<?=$endYear?>"><i class='bx bxs-file'></i> Pengantaran</a>
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=sls_year_excel&start=<?=$startYear?>&end=<?=$endYear?>"><i class='bx bxs-file'></i> Penjualan</a>
                            <button type="submit">Tampilkan</button>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=sls_year_pdf&start=<?=$startYear?>&end=<?=$endYear?>"><i class='bx bxs-file-pdf'></i> Penjualan</a>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=dlv_year_pdf&start=<?=$startYear?>&end=<?=$endYear?>"><i class='bx bxs-file-pdf'></i> Pengantaran</a>
                        </div>

                        <div class="the-chart">
                            <canvas id="yearChart"></canvas>
                        </div>
                    </div>
                    <br>

                    <!-- PERBULAN -->
                    <div class="box-chart">
                        <h3 style="text-align: center;">Data Perbulan</h3>

                        <div class="chart-control">
                            <div>
                                <p>Tahun:</p>
                                <div class="input-control">
                                    <button type="button" onclick="changeValue('monthYear', -1)">-</button>
                                    <span id="monthYearDisplay"><?= $monthYear ?></span>
                                    <button type="button" onclick="changeValue('monthYear', 1)">+</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="monthYear" id="monthYear" value="<?= $monthYear ?>">

                        <div class="btn-control">
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=dlv_month_excel&month_year=<?=$monthYear?>"><i class='bx bxs-file'></i> Pengantaran</a>
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=sls_month_excel&month_year=<?=$monthYear?>"><i class='bx bxs-file'></i> Penjualan</a>
                            <button type="submit">Tampilkan</button>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=sls_month_pdf&month_year=<?=$monthYear?>"><i class='bx bxs-file-pdf'></i> Penjualan</a>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=dlv_month_pdf&month_year=<?=$monthYear?>"><i class='bx bxs-file-pdf'></i> Pengantaran</a>
                        </div>

                        <div class="the-chart">
                            <canvas id="monthChart"></canvas>
                        </div>
                    </div>
                    <br>

                    <!-- PERHARI -->
                    <div class="box-chart">
                        <h3 style="text-align: center;">Data Perhari</h3>

                        <div class="chart-control">
                            <div>
                                <p>Tahun:</p>
                                <div class="input-control">
                                    <button type="button" onclick="changeValue('dayYear', -1)">-</button>
                                    <span id="dayYearDisplay"><?= $dailyYear ?></span>
                                    <button type="button" onclick="changeValue('dayYear', 1)">+</button>
                                </div>
                            </div>
                            <div>
                                <p>Bulan:</p>
                                <div class="input-control">
                                    <button type="button" onclick="changeValue('dayMonth', -1)">-</button>
                                    <span id="dayMonthDisplay"><?= $dailyMonth ?></span>
                                    <button type="button" onclick="changeValue('dayMonth', 1)">+</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="dayYear" id="dayYear" value="<?= $dailyYear ?>">
                        <input type="hidden" name="dayMonth" id="dayMonth" value="<?= $dailyMonth ?>">

                        <div class="btn-control">
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=dlv_date_excel&day_year=<?=$dailyYear?>&day_month=<?=$dailyMonth?>"><i class='bx bxs-file'></i> Pengantaran</a>
                            <a class="cetak-excel" href="<?=base_url()?>/process/print.php?type=sls_date_excel&day_year=<?=$dailyYear?>&day_month=<?=$dailyMonth?>"><i class='bx bxs-file'></i> Penjualan</a>
                            <button type="submit">Tampilkan</button>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=sls_date_pdf&day_year=<?=$dailyYear?>&day_month=<?=$dailyMonth?>"><i class='bx bxs-file-pdf'></i> Penjualan</a>
                            <a class="cetak-pdf" href="<?=base_url()?>/process/print.php?type=dlv_date_pdf&day_year=<?=$dailyYear?>&day_month=<?=$dailyMonth?>"><i class='bx bxs-file-pdf'></i> Pengantaran</a>
                        </div>

                        <div class="the-chart">
                            <canvas id="dateChart"></canvas>
                        </div>
                    </div>
                </form>
            </section>
        </main>

        <?php
            include "components/afterLoad.php";
        ?>

        <script>
            const yearChart = new Chart(document.getElementById('yearChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(array_keys($yearData)) ?>,
                    datasets: [
                        {
                            label: 'Total Penjualan',
                            data: <?= json_encode(array_values($yearData)) ?>,
                            borderColor: 'rgb(38, 166, 153)',
                            backgroundColor: 'rgba(38, 166, 153, 0.5)',
                            borderWidth: 2,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Pengantaran',
                            data: <?= json_encode(array_values($deliveryPerYear)) ?>,
                            borderColor: 'rgb(0, 123, 255)',
                            backgroundColor: 'rgba(0, 123, 255, 0.3)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Total Qty/Box',
                            data: <?= json_encode(array_values($qtyBoxPerYear)) ?>,
                            borderColor: 'rgb(255, 152, 0)',
                            backgroundColor: 'rgba(255, 152, 0, 0.3)',
                            borderWidth: 2,
                            borderDash: [6, 6],
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Total Penjualan (Rp)'
                            },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Qty/Box & Total Pengantaran'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            const monthChart = new Chart(document.getElementById('monthChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Total Penjualan',
                            data: <?= json_encode($monthData) ?>,
                            borderColor: 'rgb(38, 166, 153)',
                            backgroundColor: 'rgba(38, 166, 153, 0.5)',
                            borderWidth: 2,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Pengantaran',
                            data: <?= json_encode($deliveryPerMonth) ?>,
                            borderColor: 'rgb(0, 123, 255)',
                            backgroundColor: 'rgba(0, 123, 255, 0.3)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Total Qty/Box',
                            data: <?= json_encode($qtyBoxPerMonth) ?>,
                            borderColor: 'rgb(255, 152, 0)',
                            backgroundColor: 'rgba(255, 152, 0, 0.3)',
                            borderWidth: 2,
                            borderDash: [6, 6],
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Total Penjualan (Rp)'
                            },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Qty/Box & Total Pengantaran'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            const dateChart = new Chart(document.getElementById('dateChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode(range(1, $daysInMonth)) ?>,
                    datasets: [
                        {
                            label: 'Total Penjualan',
                            data: <?= json_encode($dayData) ?>,
                            borderColor: 'rgb(49, 163, 152)',
                            backgroundColor: 'rgba(38, 166, 153, 0.5)',
                            borderWidth: 2,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Total Pengantaran',
                            data: <?= json_encode($deliveryPerDay) ?>,
                            borderColor: 'rgb(0, 123, 255)',
                            backgroundColor: 'rgba(0, 123, 255, 0.3)',
                            borderWidth: 2,
                            yAxisID: 'y1'
                        },
                        {
                            label: 'Total Qty/Box',
                            data: <?= json_encode($qtyBoxPerDay) ?>,
                            borderColor: 'rgb(255, 152, 0)',
                            backgroundColor: 'rgba(255, 152, 0, 0.3)',
                            borderWidth: 2,
                            borderDash: [6, 6],
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Total Penjualan (Rp)'
                            },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Qty/Box & Total Pengantaran'
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });

            const currentYear = new Date().getFullYear();
            const currentMonth = new Date().getMonth() + 1;

            function changeValue(type, delta) {
                const input = document.getElementById(type);
                const display = document.getElementById(type + "Display");
                let value = parseInt(input.value);

                if (type === 'startYear') {
                    const endYear = parseInt(document.getElementById('endYear').value);
                    const max = endYear - 1;
                    const min = 2000;
                    value = Math.min(Math.max(value + delta, min), max);
                } else if (type === 'endYear') {
                    const startYear = parseInt(document.getElementById('startYear').value);
                    const min = startYear + 1;
                    const max = currentYear;
                    value = Math.min(Math.max(value + delta, min), max);
                } else if (type === 'monthYear') {
                    value = Math.min(Math.max(value + delta, 2000), currentYear);
                } else if (type === 'dayYear') {
                    value = Math.min(Math.max(value + delta, 2000), currentYear);
                    const dayMonthInput = document.getElementById('dayMonth');
                    const dayMonthDisplay = document.getElementById('dayMonthDisplay');
                    let dayMonthValue = parseInt(dayMonthInput.value);
                    const maxMonth = (value === currentYear) ? currentMonth : 12;
                    if (dayMonthValue > maxMonth) {
                        dayMonthValue = maxMonth;
                        dayMonthInput.value = dayMonthValue;
                        dayMonthDisplay.textContent = dayMonthValue;
                    }
                } else if (type === 'dayMonth') {
                    const selectedYear = parseInt(document.getElementById('dayYear').value);
                    const maxMonth = (selectedYear === currentYear) ? currentMonth : 12;
                    value = Math.min(Math.max(value + delta, 1), maxMonth);
                }

                input.value = value;
                display.textContent = value;
            }
        </script>
    </body>
</html>
<?php
    } else {
        echo "<script>window.location='".base_url()."/login.php';</script>";
    }
?>