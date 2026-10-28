<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">My Attendance History</h3>
        <p class="text-muted small mb-0">Complete historical attendance log, timestamps, and punctuality status</p>
    </div>
    <a href="<?= Url::to('/student/statistics') ?>" class="btn btn-outline-primary rounded-pill px-3">
        <i class="bi bi-pie-chart-fill me-1"></i> View Analytics & Stats
    </a>
</div>

<!-- Filters Bar -->
<div class="card card-custom p-3 mb-4">
    <form action="<?= Url::to('/student/history') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Specific Date</label>
            <input type="date" name="date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($date ?? '') ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Specific Month (YYYY-MM)</label>
            <input type="month" name="month" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($month ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Status</label>
            <select name="status" class="form-select form-select-sm bg-light">
                <option value="">-- All Statuses --</option>
                <option value="checked_in" <?= ($status === 'checked_in') ? 'selected' : '' ?>>Checked In</option>
                <option value="checked_out" <?= ($status === 'checked_out') ? 'selected' : '' ?>>Checked Out</option>
                <option value="late" <?= ($status === 'late') ? 'selected' : '' ?>>Late</option>
                <option value="on_leave" <?= ($status === 'on_leave') ? 'selected' : '' ?>>On Leave</option>
            </select>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
        </div>
    </form>
</div>

<!-- Attendance History Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Verification Match</th>
                    <th>Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($records)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                            No attendance history matching filters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td class="fw-semibold text-dark"><?= htmlspecialchars($r['attendance_date']) ?></td>
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
                            <td class="small text-muted"><?= htmlspecialchars($r['remarks'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
