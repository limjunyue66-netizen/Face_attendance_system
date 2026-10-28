<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Class Management</h3>
        <p class="text-muted small mb-0">Define class schedules, late grace thresholds, and rest periods</p>
    </div>
    <a href="<?= Url::to('/admin/classes/create') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Create Class
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Code</th>
                    <th>Start Time & Grace</th>
                    <th>Rest / Break Window</th>
                    <th>Enrollment</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard fs-2 d-block mb-2"></i>
                            No classes created yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($classes as $c): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($c['class_name']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($c['class_code']) ?></span></td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-alarm text-primary me-1"></i> <?= htmlspecialchars($c['start_time']) ?></div>
                                <div class="small text-muted">+<?= $c['late_grace_minutes'] ?> mins grace cutoff</div>
                            </td>
                            <td>
                                <div class="small text-dark fw-medium">
                                    <i class="bi bi-cup-hot text-warning me-1"></i> <?= htmlspecialchars($c['rest_start_time']) ?> – <?= htmlspecialchars($c['rest_end_time']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="small text-dark"><strong><?= $c['student_count'] ?? 0 ?></strong> students</div>
                                <div class="small text-muted"><?= $c['subject_count'] ?? 0 ?> subjects assigned</div>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($c['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($c['status'] === 'active') ? 'success' : 'danger' ?>">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= Url::to("/admin/classes/{$c['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $c['id'] ?>, '<?= htmlspecialchars(addslashes($c['class_name'])) ?>')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <?= Csrf::field() ?>
</form>

<script>
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete class "${name}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= Url::to('/admin/classes') ?>/${id}/delete`;
            form.submit();
        }
    }
</script>
