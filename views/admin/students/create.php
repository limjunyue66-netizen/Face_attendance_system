<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Add New Student</h4>
                        <p class="text-muted small mb-0">Enroll a student and configure credentials</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/students') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <form action="<?= Url::to('/admin/students') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="student_code" class="form-label small fw-semibold text-dark">Student Code / Matric No. <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="student_code" name="student_code" placeholder="e.g. STU-2026-005" required>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label small fw-semibold text-dark">Full Legal Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="e.g. Eleanor Vance" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-dark">Student Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="student@school.edu" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold text-dark">Contact Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="+1 555-0199">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="class_id" class="form-label small fw-semibold text-dark">Assigned Class <span class="text-danger">*</span></label>
                        <select class="form-select" id="class_id" name="class_id" required>
                            <option value="">-- Select Class --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="gender" class="form-label small fw-semibold text-dark">Gender <span class="text-danger">*</span></label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date_of_birth" class="form-label small fw-semibold text-dark">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border mb-4">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" id="create_portal_account" name="create_portal_account" value="1" checked onchange="togglePortalFields(this.checked)">
                        <label class="form-check-label fw-semibold text-dark" for="create_portal_account">
                            Create Student Web Portal Login Account
                        </label>
                    </div>
                    <div id="portalFields" class="row g-2 mt-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Portal Username (Optional, defaults to student code)</label>
                            <input type="text" class="form-control form-control-sm" name="username" placeholder="Leave empty for auto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Initial Password (Defaults to student123)</label>
                            <input type="password" class="form-control form-control-sm" name="password" placeholder="Defaults to student123">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/students') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Student
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePortalFields(enabled) {
        document.getElementById('portalFields').style.display = enabled ? 'flex' : 'none';
    }
</script>
