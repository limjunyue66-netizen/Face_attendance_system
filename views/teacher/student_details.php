<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <?php if (!empty($student['preview_photo'])): ?>
            <img src="<?= Url::asset($student['preview_photo']) ?>" class="avatar avatar-lg shadow-sm" alt="Avatar">
        <?php else: ?>
            <div class="avatar avatar-lg bg-primary text-white shadow-sm">
                <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($student['full_name']) ?></h3>
            <div class="text-muted small">
                <span class="font-monospace text-primary fw-semibold"><?= htmlspecialchars($student['student_code']) ?></span> &bull; 
                Class: <strong><?= htmlspecialchars($student['class_name'] ?? 'N/A') ?></strong>
            </div>
        </div>
    </div>
    <a href="<?= Url::to('/teacher/attendance') ?>" class="btn btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Attendance
    </a>
</div>

<!-- Performance Cards -->
<div class="row g-3 mb-4 text-center">
    <div class="col-sm-3 col-6">
        <div class="card card-custom p-3">
            <div class="small text-muted fw-semibold">Attendance Rate</div>
            <div class="h3 fw-bold text-primary my-1"><?= $statistics['percentage'] ?>%</div>
            <span class="small text-muted"><?= $statistics['present_days'] ?> / <?= $statistics['total_days'] ?> Days</span>
        </div>
    </div>
    <div class="col-sm-3 col-6">
        <div class="card card-custom p-3">
            <div class="small text-muted fw-semibold">On-Time Days</div>
            <div class="h3 fw-bold text-success my-1"><?= $statistics['on_time_days'] ?></div>
            <span class="small text-success">Punctual</span>
        </div>
    </div>
    <div class="col-sm-3 col-6">
        <div class="card card-custom p-3">
            <div class="small text-muted fw-semibold">Late Days</div>
            <div class="h3 fw-bold text-warning my-1"><?= $statistics['late_days'] ?></div>
            <span class="small text-warning">Grace Exceeded</span>
        </div>
    </div>
    <div class="col-sm-3 col-6">
        <div class="card card-custom p-3">
            <div class="small text-muted fw-semibold">Leave Days</div>
            <div class="h3 fw-bold text-info my-1"><?= $statistics['leave_days'] ?></div>
            <span class="small text-info">Approved Absences</span>
        </div>
    </div>
</div>

<div class="card card-custom">
    <div class="p-3 border-bottom">
        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-calendar2-check text-primary"></i> Detailed Attendance Record
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Confidence</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr><td colspan="6" class="text-center py-4 text-muted">No attendance logs found for this student.</td></tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
                            <td><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($r['check_out_time'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($r['check_in_confidence'])): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        <?= round($r['check_in_confidence'] * 100, 1) ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status status-<?= $r['status'] ?>">
                                    <?= ucfirst(str_replace('_', ' ', $r['status'])) ?>
                                </span>
                            </td>
                            <td class="small text-muted"><?= htmlspecialchars($r['remarks'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
