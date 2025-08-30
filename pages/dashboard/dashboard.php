<!DOCTYPE html>
<html>

<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
} else {
    ob_start();
    include('../head_css.php');
?>
<body class="skin-black">

<?php include "../connection.php"; ?>
<?php include('../header.php'); ?>

<div class="wrapper row-offcanvas row-offcanvas-left">
    <?php include('../sidebar-left.php'); ?>

    <aside class="right-side">
    <section class="content-header">
    <h1>
        Welcome back, <strong><?php echo htmlspecialchars($role); ?></strong>!
    </h1>
</section>

        <section class="content">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <!-- Total Residents -->
                        <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                            <div class="info-box card shadow-lg rounded-xl text-white" style="background: linear-gradient(135deg, #4F86F0, #89CFF0);">
                                <a href="../resident/resident.php">
                                    <span class="info-box-icon bg-transparent" style="color:rgb(4, 6, 7);">
                                        <i class="fa fa-users fa-1x"></i>
                                    </span>
                                </a>
                                <div class="info-box-content">
                                    <span class="info-box-text text-black font-semibold">Total Residents</span>
                                    <span class="info-box-number text-black text-5xl font-bold">
                                        <?php
                                        $res_q = mysqli_query($con, "SELECT * from tblresident");
                                        $resident_count = mysqli_num_rows($res_q);
                                        echo $resident_count;
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Clearances -->
                        <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                            <div class="info-box card shadow-lg rounded-xl text-white" style="background: linear-gradient(135deg, #64D4A1, #A8E4A1);">
                                <a href="../clearances/clearances.php">
                                    <span class="info-box-icon bg-transparent" style="color:rgb(6, 12, 7);">
                                        <i class="fa fa-file-alt fa-1x"></i>
                                    </span>
                                </a>
                                <div class="info-box-content">
                                    <span class="info-box-text text-black font-semibold">Total Clearances</span>
                                    <span class="info-box-number text-black text-5xl font-bold">
                                        <?php
                                        $clear_q = mysqli_query($con, "SELECT * from barangay_clearances");
                                        $clearance_count = mysqli_num_rows($clear_q);
                                        echo $clearance_count;
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Total Pending -->
                        <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                            <div class="info-box card shadow-lg rounded-xl text-white" style="background: linear-gradient(135deg, #FFD55C, #F9C35C);">
                                <a href="../blotter/blotter.php">
                                    <span class="info-box-icon bg-transparent" style="color:rgb(8, 6, 3);">
                                        <i class="fa fa-clock fa-1x"></i>
                                    </span>
                                </a>
                                <div class="info-box-content">
                                    <span class="info-box-text text-black font-semibold">Total Pending</span>
                                    <span class="info-box-number text-black text-5xl font-bold">
                                        <?php
                                        $pending_q = mysqli_query($con, "SELECT * from blotter_records WHERE status = 'Pending'");
                                        $pending_count = mysqli_num_rows($pending_q);
                                        echo $pending_count;
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Reports -->
                        <div class="col-md-3 col-sm-6 col-xs-12 mb-3">
                            <div class="info-box card shadow-lg rounded-xl text-white" style="background: linear-gradient(135deg, #FF6F61, #FF9A8B);">
                                <a href="../reports/financial_reports.php">
                                    <span class="info-box-icon bg-transparent" style="color:rgb(12, 4, 5);">
                                        <i class="fa fa-chart-line fa-1x"></i>
                                    </span>
                                </a>
                                <div class="info-box-content">
                                    <span class="info-box-text text-black font-semibold">Financial Reports</span>
                                    <span class="info-box-number text-black text-5xl font-bold">
                                        <?php
                                        $incomeTotal = 0;
                                        $expenseTotal = 0;

                                        $amountQuery = mysqli_query($con, "SELECT LOWER(TRIM(report_type)) AS type, amount FROM financial_reports");
                                        if ($amountQuery && mysqli_num_rows($amountQuery) > 0) {
                                            while ($row = mysqli_fetch_assoc($amountQuery)) {
                                                $type = $row['type'];
                                                $amount = floatval($row['amount']);

                                                if ($type === 'income' || $type === 'budget') {
                                                    $incomeTotal += $amount;
                                                }

                                                if ($type === 'expense' || $type === 'expenses') {
                                                    $expenseTotal += $amount;
                                                }
                                            }
                                        }
                                        $totalamount = $incomeTotal - $expenseTotal;
                                        echo "₱" . number_format($totalamount, 2);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphs -->
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="box card shadow-lg bg-light rounded-xl">
                                <div class="box-header">
                                    <h3 class="box-title">Records Summary</h3>
                                </div>
                                  <hr>
                                <div class="box-body" style="height: 300px;">
                                    <canvas id="lineChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <div class="box card shadow-lg bg-light rounded-xl">
                                <div class="box-header">
                                    <h3 class="box-title">Income vs Expenses</h3>
                                </div>
                                  <hr>
                                <div class="box-body" style="height: 300px;">
                                    <canvas id="barChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- /.row -->
        </section>
    </aside>
</div><!-- ./wrapper -->

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    // Bar Chart (Income vs Expenses)
    var ctxBar = document.getElementById('barChart').getContext('2d');
    var barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Income', 'Expenses'],
            datasets: [{
                label: 'Amount (₱)',
                data: [<?php echo $incomeTotal; ?>, <?php echo $expenseTotal; ?>],
                backgroundColor: ['#00D1B2', '#FF3860'],  // Updated colors for better contrast
                borderColor: ['#00B39B', '#FF2D5C'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            family: 'Arial, sans-serif',
                            weight: '600',
                        },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 16,
                        weight: '700',
                    },
                    bodyFont: {
                        size: 14,
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        font: {
                            size: 12,
                        },
                    },
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                        },
                    },
                }
            },
            layout: {
                padding: 20, // Padding around chart
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
        }
    });

    // Line Chart (Records Summary)
    var ctxLine = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Residents', 'Pending Blotter', 'Clearances'],
            datasets: [{
                label: 'Records',
                data: [<?php echo $resident_count; ?>, <?php echo $pending_count; ?>, <?php echo $clearance_count; ?>],
                backgroundColor: 'rgba(0, 123, 255, 0.2)',  // Lighter background
                borderColor: '#007bff',  // Strong border color
                borderWidth: 2,
                pointBackgroundColor: '#007bff',  // Blue points for data markers
                pointRadius: 5,
                pointHoverRadius: 7,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 14,
                            family: 'Arial, sans-serif',
                            weight: '600',
                        },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 16,
                        weight: '700',
                    },
                    bodyFont: {
                        size: 14,
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        beginAtZero: true,
                        font: {
                            size: 12,
                        },
                    },
                },
                x: {
                    ticks: {
                        font: {
                            size: 12,
                        },
                    },
                }
            },
            layout: {
                padding: 20, // Padding around chart
            },
            animation: {
                duration: 1000,
                easing: 'easeOutQuart'
            },
        }
    });
</script>

<?php }
include "../footer.php"; ?>

<script type="text/javascript">
    $(function() {
        $("#table").dataTable({
           "aoColumnDefs": [ { "bSortable": false, "aTargets": [ 0,5 ] } ],"aaSorting": []
        });
    });
</script>

</body>
</html>
