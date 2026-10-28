<?php
use App\Helpers\Url;
?>
<div class="page-header-box">
    <div>
        <h3 class="page-title"><i class="bi bi-calendar2-check-fill text-primary"></i> Attendance Records</h3>
        <p class="page-subtitle">Biometric facial verification logs, check-in and check-out timestamps</p>
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <a href="<?= Url::to('/admin/attendance/export?format=csv' . (!empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '')) ?>" class="btn btn-outline-secondary rounded-pill-start">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV
            </a>
            <a href="<?= Url::to('/admin/attendance/export?format=excel' . (!empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : '')) ?>" class="btn btn-outline-success rounded-pill-end">
                <i class="bi bi-file-earmark-excel me-1"></i> Excel
            </a>
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="filter-toolbar">
    <form action="<?= Url::to('/admin/attendance') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($date ?? '') ?>">
            </div>
        </div>

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <select name="class_id" class="form-select">
                    <option value="">-- All Classes --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (!empty($classId) && (int)$classId === (int)$c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['class_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <select name="student_id" class="form-select">
                    <option value="">-- All Students --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= (!empty($studentId) && (int)$studentId === (int)$s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['full_name']) ?> (<?= $s['student_code'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-flag"></i></span>
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="checked_in" <?= ($status === 'checked_in') ? 'selected' : '' ?>>Checked In</option>
                    <option value="checked_out" <?= ($status === 'checked_out') ? 'selected' : '' ?>>Checked Out</option>
                    <option value="late" <?= ($status === 'late') ? 'selected' : '' ?>>Late</option>
                    <option value="absent" <?= ($status === 'absent') ? 'selected' : '' ?>>Absent</option>
                    <option value="on_leave" <?= ($status === 'on_leave') ? 'selected' : '' ?>>On Leave</option>
                </select>
            </div>
        </div>

        <div class="col-md-1 d-flex gap-1">
            <button type="submit" class="btn btn-primary w-100" title="Apply Filter"><i class="bi bi-funnel-fill"></i></button>
            <?php if (!empty($date) || !empty($classId) || !empty($studentId) || !empty($status)): ?>
                <a href="<?= Url::to('/admin/attendance') ?>" class="btn btn-light" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Attendance Records Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th><i class="bi bi-calendar3 me-1"></i> Date</th>
                    <th><i class="bi bi-person-fill me-1"></i> Student Name & Code</th>
                    <th><i class="bi bi-mortarboard-fill me-1"></i> Class</th>
                    <th><i class="bi bi-box-arrow-in-right me-1 text-success"></i> Check In</th>
                    <th><i class="bi bi-box-arrow-right me-1 text-primary"></i> Check Out</th>
                    <th><i class="bi bi-cpu me-1"></i> Confidence</th>
                    <th><i class="bi bi-shield-check me-1"></i> Status</th>
                    <th><i class="bi bi-camera me-1"></i> Snapshots</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar2-x fs-2 d-block mb-2"></i>
                            No attendance records found matching filters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><i class="bi bi-calendar-event me-1 text-muted"></i><?= htmlspecialchars($r['attendance_date']) ?></td>
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
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($r['student_name']) ?></div>
                                        <div class="small text-muted font-monospace"><i class="bi bi-tag me-1"></i><?= htmlspecialchars($r['student_code']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($r['class_name'] ?? 'N/A') ?></span></td>
                            <td>
                                <span class="fw-semibold text-dark"><i class="bi bi-clock me-1 text-success"></i><?= htmlspecialchars($r['check_in_time'] ?? '-') ?></span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark"><i class="bi bi-clock-history me-1 text-primary"></i><?= htmlspecialchars($r['check_out_time'] ?? '-') ?></span>
                            </td>
                            <td>
                                <?php if (!empty($r['check_in_confidence'])): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        <i class="bi bi-check-all me-1"></i><?= round($r['check_in_confidence'] * 100, 1) ?>%
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
                            <td>
                                <div class="d-flex gap-1">
                                    <?php if (!empty($r['check_in_photo'])): ?>
                                        <a href="<?= Url::asset($r['check_in_photo']) ?>" target="_blank" class="btn btn-sm btn-light border py-0 px-2" title="View Check-In Photo">
                                            <i class="bi bi-camera me-1"></i> In
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($r['check_out_photo'])): ?>
                                        <a href="<?= Url::asset($r['check_out_photo']) ?>" target="_blank" class="btn btn-sm btn-light border py-0 px-2" title="View Check-Out Photo">
                                            <i class="bi bi-camera-fill me-1"></i> Out
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
