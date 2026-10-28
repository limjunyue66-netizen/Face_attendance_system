<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Attendance Analytics & Statistics</h3>
        <p class="text-muted small mb-0">Detailed breakdown of punctuality, absence rates, and monthly performance</p>
    </div>
    <a href="<?= Url::to('/student/history') ?>" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-clock-history me-1"></i> View Log Records
    </a>
</div>

<!-- Primary Stat Cards -->
<div class="row g-3 mb-4 text-center">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-pie-chart-fill"></i>
            </div>
            <div>
                <div class="stat-title">Attendance Rate</div>
                <div class="stat-value"><?= $statistics['percentage'] ?>%</div>
                <div class="small text-muted mt-1"><?= $statistics['present_days'] ?> of <?= $statistics['total_days'] ?> recorded days</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="stat-title">On-Time Days</div>
                <div class="stat-value"><?= $statistics['on_time_days'] ?></div>
                <div class="small text-success mt-1">Punctual arrival</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <div class="stat-title">Late Arrivals</div>
                <div class="stat-value"><?= $statistics['late_days'] ?></div>
                <div class="small text-warning mt-1">Exceeded grace cutoff</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="bi bi-file-earmark-medical"></i>
            </div>
            <div>
                <div class="stat-title">Approved Leaves</div>
                <div class="stat-value"><?= $statistics['leave_days'] ?></div>
                <div class="small text-info mt-1">Excused absences</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-5">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-donut-chart text-primary"></i> Attendance Distribution
            </h6>
            <div style="max-height: 280px; position: relative;">
                <canvas id="attendanceDoughnutChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-line text-success"></i> Monthly Attendance (<?= $year ?>)
            </h6>
            <div style="height: 280px;">
                <canvas id="monthlyAttendanceBarChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Doughnut Chart
    const ctxDoughnut = document.getElementById('attendanceDoughnutChart');
    if (ctxDoughnut) {
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['On-Time', 'Late', 'Leave', 'Absent'],
                datasets: [{
                    data: [
                        <?= $statistics['on_time_days'] ?>,
                        <?= $statistics['late_days'] ?>,
                        <?= $statistics['leave_days'] ?>,
                        <?= $statistics['absent_days'] ?>
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#06b6d4', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // 2. Monthly Bar Chart
    const ctxBar = document.getElementById('monthlyAttendanceBarChart');
    if (ctxBar) {
        <?php
        $months = [];
        $onTimes = [];
        $lates = [];
        foreach ($monthlyData as $md) {
            $months[] = $md['month_name'];
            $onTimes[] = (int)$md['on_time'];
            $lates[] = (int)$md['late'];
        }
        if (empty($months)) {
            $months = [date('M')];
            $onTimes = [$statistics['on_time_days']];
            $lates = [$statistics['late_days']];
        }
        ?>
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?= json_encode($months) ?>,
                datasets: [
                    {
                        label: 'On-Time',
                        data: <?= json_encode($onTimes) ?>,
                        backgroundColor: '#10b981',
                        borderRadius: 6
                    },
                    {
                        label: 'Late',
                        data: <?= json_encode($lates) ?>,
                        backgroundColor: '#f59e0b',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>
