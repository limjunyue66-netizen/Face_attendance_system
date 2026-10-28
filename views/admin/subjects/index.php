<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Subject Management</h3>
        <p class="text-muted small mb-0">Academic curriculum course codes, titles, and syllabi</p>
    </div>
    <a href="<?= Url::to('/admin/subjects/create') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-journal-plus me-1"></i> Add Subject
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                            No curriculum subjects registered yet.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($subjects as $s): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($s['subject_code']) ?></span></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($s['subject_name']) ?></td>
                            <td class="small text-muted"><?= htmlspecialchars($s['description'] ?? '-') ?></td>
                            <td>
                                <span class="badge bg-<?= ($s['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($s['status'] === 'active') ? 'success' : 'danger' ?>">
                                    <?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= Url::to("/admin/subjects/{$s['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['subject_name'])) ?>')" title="Delete">
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
        if (confirm(`Are you sure you want to delete subject "${name}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= Url::to('/admin/subjects') ?>/${id}/delete`;
            form.submit();
        }
    }
</script>
