<?php
use App\Helpers\Url;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Student Leave Applications</h3>
        <p class="text-muted small mb-0">Review student absence requests and submitted medical/supporting documents</p>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="card card-custom p-3 mb-4">
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/admin/leaves') ?>" class="btn btn-sm <?= empty($status) ? 'btn-primary' : 'btn-light' ?> rounded-pill px-3">
            All Applications
        </a>
        <a href="<?= Url::to('/admin/leaves?status=pending') ?>" class="btn btn-sm <?= ($status === 'pending') ? 'btn-warning' : 'btn-light' ?> rounded-pill px-3">
            <i class="bi bi-clock-history me-1"></i> Pending Only
        </a>
        <a href="<?= Url::to('/admin/leaves?status=approved') ?>" class="btn btn-sm <?= ($status === 'approved') ? 'btn-success' : 'btn-light' ?> rounded-pill px-3">
            <i class="bi bi-check-circle me-1"></i> Approved
        </a>
        <a href="<?= Url::to('/admin/leaves?status=rejected') ?>" class="btn btn-sm <?= ($status === 'rejected') ? 'btn-danger' : 'btn-light' ?> rounded-pill px-3">
            <i class="bi bi-x-circle me-1"></i> Rejected
        </a>
    </div>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Leave Date</th>
                    <th>Student Name & Code</th>
                    <th>Class</th>
                    <th>Reason</th>
                    <th>Supporting Doc</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($leaves)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-envelope-paper fs-2 d-block mb-2"></i>
                            No leave applications found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($leaves as $l): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($l['leave_date']) ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($l['student_name']) ?></div>
                                <div class="small text-muted font-monospace"><?= htmlspecialchars($l['student_code']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($l['class_name'] ?? 'N/A') ?></span></td>
                            <td class="text-truncate" style="max-width: 240px;">
                                <span class="small text-muted"><?= htmlspecialchars($l['reason']) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($l['document_path'])): ?>
                                    <a href="<?= Url::asset($l['document_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill small">
                                        <i class="bi bi-paperclip me-1"></i> <?= htmlspecialchars($l['original_filename'] ?: 'Document') ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">None attached</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status status-<?= $l['status'] ?>">
                                    <?= ucfirst($l['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= Url::to("/admin/leaves/{$l['id']}") ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                                    Review &rarr;
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
