<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Rest & Break Attendance</h3>
        <p class="text-muted small mb-0">Midday recess / lunch break departure and return tracking</p>
    </div>
    <a href="<?= Url::to('/admin/attendance') ?>" class="btn btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Back to Main Attendance
    </a>
</div>

<!-- Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form action="<?= Url::to('/admin/attendance/rest') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Date</label>
            <input type="date" name="date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($date ?? '') ?>">
        </div>
        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Class</label>
            <select name="class_id" class="form-select form-select-sm bg-light">
                <option value="">-- All Classes --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (!empty($classId) && (int)$classId === (int)$c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Rest Break Check-In</th>
                    <th>Return Check-Out</th>
                    <th>Confidence</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-cup-straw fs-2 d-block mb-2"></i>
                            No rest break attendance entries recorded.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($r['student_name']) ?></div>
                                <div class="small text-muted font-monospace"><?= htmlspecialchars($r['student_code']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($r['class_name'] ?? 'N/A') ?></span></td>
                            <td>
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle">
                                    <i class="bi bi-box-arrow-right me-1"></i> <?= htmlspecialchars($r['rest_check_in_time'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    <i class="bi bi-box-arrow-in-left me-1"></i> <?= htmlspecialchars($r['rest_check_out_time'] ?? 'Pending Return') ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($r['rest_check_in_confidence'])): ?>
                                    <span class="small text-muted"><?= round($r['rest_check_in_confidence'] * 100, 1) ?>%</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
