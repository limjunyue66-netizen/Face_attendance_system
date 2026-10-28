<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="page-header-box">
    <div>
        <h3 class="page-title"><i class="bi bi-people-fill text-primary"></i> Student Directory</h3>
        <p class="page-subtitle">Manage student enrollment, facial recognition registration, and class assignments</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= Url::to('/admin/students/export' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')) ?>" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </a>
        <a href="<?= Url::to('/admin/students/create') ?>" class="btn btn-primary rounded-pill px-3 shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Add Student
        </a>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-toolbar">
    <form action="<?= Url::to('/admin/students') ?>" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name, student code or email..." value="<?= htmlspecialchars($search ?? '') ?>">
            </div>
        </div>

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-mortarboard"></i></span>
                <select name="class_id" class="form-select">
                    <option value="">-- All Classes --</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (!empty($classId) && (int)$classId === (int)$c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['class_name']) ?> (<?= htmlspecialchars($c['class_code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-toggle-on"></i></span>
                <select name="status" class="form-select">
                    <option value="">-- All Statuses --</option>
                    <option value="active" <?= ($status === 'active') ? 'selected' : '' ?>>Active Only</option>
                    <option value="inactive" <?= ($status === 'inactive') ? 'selected' : '' ?>>Inactive Only</option>
                </select>
            </div>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
            <?php if (!empty($search) || !empty($classId) || !empty($status)): ?>
                <a href="<?= Url::to('/admin/students') ?>" class="btn btn-light" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Student Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th><i class="bi bi-person-badge me-1"></i> Student Information</th>
                    <th><i class="bi bi-mortarboard me-1"></i> Class</th>
                    <th><i class="bi bi-gender-ambiguous me-1"></i> Gender & DOB</th>
                    <th><i class="bi bi-person-bounding-box me-1"></i> Biometric Status</th>
                    <th><i class="bi bi-activity me-1"></i> Account</th>
                    <th class="text-end"><i class="bi bi-gear me-1"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            No students found matching your criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <?php if (!empty($s['preview_photo'])): ?>
                                        <img src="<?= Url::asset($s['preview_photo']) ?>" class="avatar" alt="Avatar">
                                    <?php else: ?>
                                        <div class="avatar bg-primary text-white">
                                            <?= strtoupper(substr($s['full_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?= Url::to("/admin/students/{$s['id']}") ?>" class="fw-bold text-dark text-decoration-none">
                                            <?= htmlspecialchars($s['full_name']) ?>
                                        </a>
                                        <div class="small text-muted font-monospace"><i class="bi bi-upc-scan me-1"></i><?= htmlspecialchars($s['student_code']) ?></div>
                                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($s['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border"><i class="bi bi-door-closed me-1"></i><?= htmlspecialchars($s['class_name'] ?? 'Unassigned') ?></span>
                            </td>
                            <td>
                                <div class="small text-dark text-capitalize"><i class="bi bi-person me-1 text-muted"></i><?= htmlspecialchars($s['gender'] ?? 'N/A') ?></div>
                                <div class="small text-muted"><i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($s['date_of_birth'] ?? '-') ?></div>
                            </td>
                            <td>
                                <?php if ($s['face_registered']): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-check-circle-fill"></i> Registered (v<?= $s['registration_version'] ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-exclamation-circle"></i> Not Registered
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= ($s['status'] === 'active') ? 'success' : 'danger' ?>-subtle text-<?= ($s['status'] === 'active') ? 'success' : 'danger' ?> border">
                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i><?= ucfirst($s['status']) ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= Url::to("/admin/students/{$s['id']}/face-register") ?>" class="btn btn-sm btn-outline-primary" title="<?= $s['face_registered'] ? 'Re-register Face' : 'Register Face' ?>">
                                        <i class="bi bi-camera-fill"></i>
                                    </a>
                                    <a href="<?= Url::to("/admin/students/{$s['id']}") ?>" class="btn btn-sm btn-outline-secondary" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= Url::to("/admin/students/{$s['id']}/edit") ?>" class="btn btn-sm btn-outline-secondary" title="Edit Student">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['full_name'])) ?>')" title="Delete Student">
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

<!-- Delete Form Modal -->
<form id="deleteForm" method="POST" style="display: none;">
    <?= Csrf::field() ?>
</form>

<script>
    function confirmDelete(id, name) {
        if (confirm(`Are you sure you want to delete student "${name}"? This will also remove associated face descriptors and portal login.`)) {
            const form = document.getElementById('deleteForm');
            form.action = `<?= Url::to('/admin/students') ?>/${id}/delete`;
            form.submit();
        }
    }
</script>
