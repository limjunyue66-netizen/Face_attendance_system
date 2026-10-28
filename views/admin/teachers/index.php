<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Teacher Management</h3>
        <p class="text-muted small mb-0">Manage faculty members, user accounts, and teaching assignments</p>
    </div>
    <a href="<?= Url::to('/admin/teachers/create') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Add Teacher
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Teacher</th>
                    <th>Code</th>
                    <th>Department</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($teachers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-workspace fs-2 d-block mb-2"></i>
                            No faculty teachers recorded yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($teachers as $t): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar bg-info text-white">
                                        <?= strtoupper(substr($t['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($t['full_name']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($t['email']) ?></div>
                                        <?php if (!empty($t['username'])): ?>
                                            <div class="small text-primary">Login: <code><?= htmlspecialchars($t['username']) ?></code></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($t['teacher_code']) ?></span></td>
                            <td><?= htmlspecialchars($t['department'] ?? 'General') ?></td>
                            <td><?= htmlspecialchars($t['phone'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= ($t['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($t['status'] === 'active') ? 'success' : 'danger' ?>">
                                    <?= ucfirst($t['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= Url::to("/admin/teachers/{$t['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $t['id'] ?>, '<?= htmlspecialchars(addslashes($t['full_name'])) ?>')" title="Delete">
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
        if (confirm(`Are you sure you want to delete teacher "${name}"? This will also remove portal access and teaching assignments.`)) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= Url::to('/admin/teachers') ?>/${id}/delete`;
            form.submit();
        }
    }
</script>
