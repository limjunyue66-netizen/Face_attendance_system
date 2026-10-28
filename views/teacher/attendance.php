<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Class Attendance Records</h3>
        <p class="text-muted small mb-0">Facial verification records restricted to your assigned classes</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form action="<?= Url::to('/teacher/attendance') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Filter by Class</label>
            <select name="class_id" class="form-select form-select-sm bg-light">
                <option value="">-- All My Assigned Classes --</option>
                <?php 
                $uniqueClasses = [];
                foreach ($assignments as $a) {
                    $uniqueClasses[$a['class_id']] = $a['class_name'];
                }
                foreach ($uniqueClasses as $cId => $cName): ?>
                    <option value="<?= $cId ?>" <?= ($selectedClassId === (int)$cId) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cName) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Date</label>
            <input type="date" name="date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($date ?? '') ?>">
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter Records</button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student Name & Code</th>
                    <th>Class</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Confidence</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No attendance records found matching filters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
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
                                        <a href="<?= Url::to("/teacher/students/{$r['student_id']}") ?>" class="fw-bold text-dark text-decoration-none">
                                            <?= htmlspecialchars($r['student_name']) ?>
                                        </a>
                                        <div class="small text-muted font-monospace"><?= htmlspecialchars($r['student_code']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($r['class_name']) ?></span></td>
                            <td><span class="fw-medium text-dark"><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></span></td>
                            <td><span class="fw-medium text-dark"><?= htmlspecialchars($r['check_out_time'] ?? '-') ?></span></td>
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
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
