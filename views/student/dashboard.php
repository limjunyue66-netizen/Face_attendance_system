<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Student Portal</h3>
        <p class="text-muted small mb-0">Welcome, <strong><?= htmlspecialchars($student['full_name']) ?></strong> (<?= htmlspecialchars($student['student_code']) ?>)</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/student/scan') ?>" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
            <i class="bi bi-camera-fill me-1"></i> Scan Attendance Now
        </a>
    </div>
</div>

<!-- Today's Status Banner Card -->
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="stat-icon <?= !empty($todayAttendance) ? 'success' : 'warning' ?>" style="width: 56px; height: 56px; font-size: 1.6rem;">
                <i class="bi <?= !empty($todayAttendance) ? 'bi-check-circle-fill' : 'bi-exclamation-circle' ?>"></i>
            </div>
            <div>
                <h5 class="fw-bold text-dark mb-1">Today's Attendance Status</h5>
                <?php if ($hasApprovedLeave): ?>
                    <span class="badge bg-purple-subtle text-purple fs-6">
                        <i class="bi bi-file-earmark-medical me-1"></i> Approved On Leave Today
                    </span>
                <?php elseif (!empty($todayAttendance)): ?>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge-status status-<?= $todayAttendance['status'] ?> fs-6">
                            <?= ucfirst(str_replace('_', ' ', $todayAttendance['status'])) ?>
                        </span>
                        <span class="text-muted small">
                            (In: <strong><?= $todayAttendance['check_in_time'] ?></strong><?= !empty($todayAttendance['check_out_time']) ? " | Out: <strong>{$todayAttendance['check_out_time']}</strong>" : "" ?>)
                        </span>
                    </div>
                <?php else: ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis fs-6">
                        Not Scanned Yet Today
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <?php if (!$student['face_registered']): ?>
                <a href="<?= Url::to('/student/face-register') ?>" class="btn btn-warning rounded-pill px-3 fw-semibold">
                    <i class="bi bi-camera me-1"></i> Enroll Face First
                </a>
            <?php else: ?>
                <a href="<?= Url::to('/student/scan') ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-camera me-1"></i> Camera Scanner
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Performance Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="bi bi-percent"></i>
            </div>
            <div>
                <div class="stat-title">Attendance Rate</div>
                <div class="stat-value"><?= $statistics['percentage'] ?>%</div>
                <div class="small text-muted mt-1"><?= $statistics['present_days'] ?> of <?= $statistics['total_days'] ?> Days</div>
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
                <div class="stat-title">Late Days</div>
                <div class="stat-value"><?= $statistics['late_days'] ?></div>
                <div class="small text-warning mt-1">Grace exceeded</div>
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
                <div class="small text-muted mt-1"><a href="<?= Url::to('/student/leaves') ?>" class="text-decoration-none text-info">Apply leave &rarr;</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Class Details & Recent Records Split -->
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-mortarboard text-primary me-1"></i> My Class Schedule
            </h6>
            <ul class="list-unstyled mb-0 small">
                <li class="mb-2">
                    <span class="text-muted d-block">Class:</span>
                    <strong class="text-dark fs-6"><?= htmlspecialchars($student['class_name'] ?? 'N/A') ?></strong>
                </li>
                <li class="mb-2">
                    <span class="text-muted d-block">Official Start Time:</span>
                    <span class="fw-semibold text-primary"><i class="bi bi-alarm me-1"></i> <?= htmlspecialchars($student['start_time'] ?? '08:00:00') ?></span>
                </li>
                <li class="mb-2">
                    <span class="text-muted d-block">Late Grace Period:</span>
                    <span><?= htmlspecialchars($student['late_grace_minutes'] ?? '15') ?> minutes allowable cutoff</span>
                </li>
                <li class="mb-3">
                    <span class="text-muted d-block">Rest / Break Window:</span>
                    <span><i class="bi bi-cup-hot text-warning me-1"></i> <?= htmlspecialchars($student['rest_start_time'] ?? '12:00:00') ?> – <?= htmlspecialchars($student['rest_end_time'] ?? '13:00:00') ?></span>
                </li>
                <li class="pt-2 border-top">
                    <span class="text-muted d-block">Biometric Status:</span>
                    <?php if ($student['face_registered']): ?>
                        <span class="text-success fw-semibold"><i class="bi bi-shield-check me-1"></i> Face Enrolled (v<?= $student['registration_version'] ?>)</span>
                    <?php else: ?>
                        <span class="text-danger fw-semibold"><i class="bi bi-exclamation-triangle me-1"></i> Face Not Enrolled</span>
                    <?php endif; ?>
                </li>
            </ul>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card card-custom h-100">
            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> Recent Attendance Records
                </h6>
                <a href="<?= Url::to('/student/history') ?>" class="small text-muted text-decoration-none">Full History</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentRecords)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">No attendance scans recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recentRecords as $r): ?>
                                <tr>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
                                    <td><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($r['check_out_time'] ?? '-') ?></td>
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
</div>
