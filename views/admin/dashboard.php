<?php
use App\Helpers\Url;
?>
<div class="page-header-box">
    <div>
        <h3 class="page-title"><i class="bi bi-speedometer2 text-primary"></i> Administrative Overview</h3>
        <p class="page-subtitle">Real-time attendance telemetry, student enrollment, and biometric status</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/kiosk') ?>" target="_blank" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-camera-video me-1"></i> Launch Kiosk
        </a>
        <a href="<?= Url::to('/admin/students/create') ?>" class="btn btn-outline-primary rounded-pill px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Add Student
        </a>
    </div>
</div>

<!-- Primary Metric Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div>
                <div class="stat-title"><i class="bi bi-mortarboard me-1"></i> Total Students</div>
                <div class="stat-value"><?= number_format($totalStudents) ?></div>
                <div class="small text-success mt-1 fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> <?= $activeStudents ?> active</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon info">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div>
                <div class="stat-title"><i class="bi bi-award me-1"></i> Faculty Teachers</div>
                <div class="stat-value"><?= number_format($totalTeachers) ?></div>
                <div class="small text-muted mt-1 fw-medium"><i class="bi bi-building me-1"></i> <?= $totalClasses ?> active classes</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="bi bi-person-bounding-box"></i>
            </div>
            <div>
                <div class="stat-title"><i class="bi bi-shield-check me-1"></i> Faces Registered</div>
                <div class="stat-value"><?= number_format($registeredFaces) ?></div>
                <?php $pct = $totalStudents > 0 ? round(($registeredFaces / $totalStudents) * 100) : 0; ?>
                <div class="small text-muted mt-1 fw-medium"><i class="bi bi-cpu me-1"></i> <?= $pct ?>% enrollment rate</div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <i class="bi bi-envelope-paper-fill"></i>
            </div>
            <div>
                <div class="stat-title"><i class="bi bi-clock-history me-1"></i> Pending Leaves</div>
                <div class="stat-value"><?= number_format($pendingLeaves) ?></div>
                <div class="small text-warning mt-1 fw-semibold"><a href="<?= Url::to('/admin/leaves') ?>" class="text-decoration-none text-warning"><i class="bi bi-arrow-right-circle me-1"></i> Review requests</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Attendance Counter Cards -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-calendar2-day-fill text-primary fs-5"></i>
            <h5 class="fw-bold text-dark mb-0">Today's Attendance Snapshot (<?= date('M j, Y') ?>)</h5>
        </div>
        <a href="<?= Url::to('/admin/attendance') ?>" class="text-decoration-none small text-primary fw-semibold d-inline-flex align-items-center gap-1">
            <span>View Detailed Log</span> <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-3 text-center">
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border border-2 shadow-xs" style="border-color: #cbd5e1 !important;">
                <div class="text-muted small fw-bold text-uppercase"><i class="bi bi-qr-code-scan me-1 text-primary"></i> Scanned Today</div>
                <div class="h2 fw-bold text-dark my-1" style="font-size: 2.1rem;"><?= $todayTotal ?></div>
                <span class="badge bg-secondary-subtle text-secondary border"><i class="bi bi-archive me-1"></i> Total Records</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border border-2 shadow-xs" style="border-color: #a7f3d0 !important;">
                <div class="text-success small fw-bold text-uppercase"><i class="bi bi-box-arrow-in-right me-1"></i> Checked In</div>
                <div class="h2 fw-bold text-success my-1" style="font-size: 2.1rem;"><?= $checkedIn ?></div>
                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-geo-alt-fill me-1"></i> On Campus</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border border-2 shadow-xs" style="border-color: #bfdbfe !important;">
                <div class="text-primary small fw-bold text-uppercase"><i class="bi bi-box-arrow-right me-1"></i> Checked Out</div>
                <div class="h2 fw-bold text-primary my-1" style="font-size: 2.1rem;"><?= $checkedOut ?></div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="bi bi-check2-all me-1"></i> Completed</span>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 rounded-3 bg-white border border-2 shadow-xs" style="border-color: #fde68a !important;">
                <div class="text-warning small fw-bold text-uppercase" style="color: #b45309 !important;"><i class="bi bi-hourglass-bottom me-1"></i> Late Arrivals</div>
                <div class="h2 fw-bold my-1" style="color: #b45309; font-size: 2.1rem;"><?= $lateCount ?></div>
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="color: #b45309 !important;"><i class="bi bi-exclamation-circle me-1"></i> Exceeded Grace</span>
            </div>
        </div>
    </div>
</div>

<!-- Split Tables: Today's Recent Scans & System Audit Log -->
<div class="row g-4">
    <!-- Today's Scans -->
    <div class="col-lg-7">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> Recent Attendance Scans
                </h6>
                <a href="<?= Url::to('/admin/attendance') ?>" class="small text-muted text-decoration-none d-flex align-items-center gap-1">
                    <span>View All</span> <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th><i class="bi bi-person-fill me-1"></i> Student</th>
                            <th><i class="bi bi-mortarboard-fill me-1"></i> Class</th>
                            <th><i class="bi bi-clock-fill me-1"></i> Time</th>
                            <th><i class="bi bi-shield-check me-1"></i> Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentAttendance)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-camera-video-off fs-3 d-block mb-1"></i>
                                    No attendance scans recorded yet today.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentAttendance as $r): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($r['preview_photo'])): ?>
                                                <img src="<?= Url::asset($r['preview_photo']) ?>" class="avatar" alt="Avatar">
                                            <?php else: ?>
                                                <div class="avatar bg-primary text-white">
                                                    <?= strtoupper(substr($r['student_name'] ?? 'S', 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold text-dark"><?= htmlspecialchars($r['student_name']) ?></div>
                                                <div class="small text-muted font-monospace"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($r['student_code']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($r['class_name'] ?? 'N/A') ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><i class="bi bi-stopwatch me-1 text-muted"></i><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></div>
                                        <?php if (!empty($r['check_in_confidence'])): ?>
                                            <div class="small text-success"><i class="bi bi-check-all me-1"></i><?= round($r['check_in_confidence'] * 100, 1) ?>% match</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-status status-<?= $r['status'] ?>">
                                            <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- System Audit Log preview -->
    <div class="col-lg-5">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-info"></i> Security & Audit Trail
                </h6>
                <a href="<?= Url::to('/admin/audit') ?>" class="small text-muted text-decoration-none d-flex align-items-center gap-1">
                    <span>Full Logs</span> <i class="bi bi-chevron-right"></i>
                </a>
            </div>
            <div class="p-3">
                <?php if (empty($recentLogs)): ?>
                    <p class="text-muted text-center py-4 mb-0"><i class="bi bi-inbox fs-4 d-block mb-1"></i>No audit events recorded.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($recentLogs as $log): ?>
                            <div class="d-flex gap-3 mb-3 pb-2 border-bottom border-light">
                                <div class="stat-icon info" style="width: 36px; height: 36px; font-size: 1rem;">
                                    <i class="bi bi-activity"></i>
                                </div>
                                <div class="overflow-hidden flex-1">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <strong class="small text-dark font-monospace"><i class="bi bi-terminal me-1 text-muted"></i><?= htmlspecialchars($log['action']) ?></strong>
                                        <span class="small text-muted" style="font-size: 0.72rem;"><i class="bi bi-clock me-1"></i><?= date('H:i:s', strtotime($log['created_at'])) ?></span>
                                    </div>
                                    <div class="small text-muted text-truncate mt-1">
                                        <i class="bi bi-person me-1"></i><?= !empty($log['username']) ? "User: " . htmlspecialchars($log['username']) : "System" ?>
                                        &bull; <i class="bi bi-hdd-network me-1"></i>IP: <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1') ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
