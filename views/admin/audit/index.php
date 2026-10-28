<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">System Audit & Security Logs</h3>
        <p class="text-muted small mb-0">Immutable compliance records for authentication, registrations, attendance, and administrative events</p>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form action="<?= Url::to('/admin/audit') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Filter by Action</label>
            <input type="text" name="action" class="form-control form-control-sm bg-light" placeholder="e.g. LOGIN, ATTENDANCE, FACE" value="<?= htmlspecialchars($action ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Start Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($startDate ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">End Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm bg-light" value="<?= htmlspecialchars($endDate ?? '') ?>">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary btn-sm w-100">Filter Logs</button>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>User / Actor</th>
                    <th>Associated Student</th>
                    <th>Network IP & Client</th>
                    <th>Event Metadata</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-x fs-2 d-block mb-2"></i>
                            No audit log entries matching filters.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td class="small text-muted font-monospace" style="white-space: nowrap;">
                                <?= htmlspecialchars($l['created_at']) ?>
                            </td>
                            <td>
                                <span class="badge bg-dark font-monospace text-uppercase" style="font-size: 0.72rem;">
                                    <?= htmlspecialchars($l['action']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($l['username'])): ?>
                                    <div class="small fw-semibold text-dark"><?= htmlspecialchars($l['username']) ?></div>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 0.65rem;"><?= htmlspecialchars($l['user_role'] ?? 'user') ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">System / Guest</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($l['student_name'])): ?>
                                    <div class="small fw-medium text-dark"><?= htmlspecialchars($l['student_name']) ?></div>
                                    <span class="small text-muted font-monospace"><?= htmlspecialchars($l['student_code']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small text-dark font-monospace"><?= htmlspecialchars($l['ip_address'] ?? '127.0.0.1') ?></div>
                                <div class="small text-muted text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($l['user_agent'] ?? '') ?>">
                                    <?= htmlspecialchars($l['user_agent'] ?? 'Browser') ?>
                                </div>
                            </td>
                            <td class="small text-muted" style="max-width: 250px;">
                                <?php if (!empty($l['metadata'])): ?>
                                    <code class="text-dark d-block text-truncate" title="<?= htmlspecialchars($l['metadata']) ?>">
                                        <?= htmlspecialchars($l['metadata']) ?>
                                    </code>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
