<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">User Account Management</h3>
        <p class="text-muted small mb-0">System credentials and portal role-based access control</p>
    </div>
    <a href="<?= Url::to('/admin/users/create') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Add User Account
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar bg-primary text-white">
                                    <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                </div>
                                <span class="fw-bold text-dark"><?= htmlspecialchars($u['username']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= ($u['role'] === 'admin') ? 'primary' : (($u['role'] === 'teacher') ? 'info' : 'secondary') ?>-subtle text-dark text-capitalize">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= ($u['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($u['status'] === 'active') ? 'success' : 'danger' ?>">
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td class="small text-muted"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a href="<?= Url::to("/admin/users/{$u['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($u['id'] !== \App\Helpers\Auth::id()): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $u['id'] ?>, '<?= htmlspecialchars(addslashes($u['username'])) ?>')" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<form id="deleteForm" method="POST" style="display: none;">
    <?= Csrf::field() ?>
</form>

<script>
    function confirmDelete(id, username) {
        if (confirm(`Are you sure you want to delete user account "${username}"?`)) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= Url::to('/admin/users') ?>/${id}/delete`;
            form.submit();
        }
    }
</script>
