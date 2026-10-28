<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon info">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Edit Teacher</h4>
                        <p class="text-muted small mb-0">Update profile for <?= htmlspecialchars($teacher['full_name']) ?></p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/teachers') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to("/admin/teachers/{$teacher['id']}") ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="teacher_code" class="form-label small fw-semibold text-dark">Teacher Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="teacher_code" name="teacher_code" value="<?= htmlspecialchars($teacher['teacher_code']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold text-dark">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($teacher['phone'] ?? '') ?>">
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <label for="department" class="form-label small fw-semibold text-dark">Academic Department <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($teacher['department'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($teacher['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($teacher['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/teachers') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Update Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
