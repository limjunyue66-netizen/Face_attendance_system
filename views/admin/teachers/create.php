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
                        <i class="bi bi-person-plus"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Add New Teacher</h4>
                        <p class="text-muted small mb-0">Create faculty account with login credentials</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/teachers') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to('/admin/teachers') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="teacher_code" class="form-label small fw-semibold text-dark">Teacher Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="teacher_code" name="teacher_code" placeholder="e.g. TCH-003" required>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="e.g. Dr. Robert Green" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="teacher@school.edu" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label small fw-semibold text-dark">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="+1 555-0188">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label for="department" class="form-label small fw-semibold text-dark">Academic Department <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="department" name="department" placeholder="e.g. Computer Science, Mathematics" required>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border mb-4">
                    <h6 class="fw-semibold text-dark small mb-2"><i class="bi bi-key me-1"></i> Teacher Portal Login Credentials</h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Username (Defaults to teacher code)</label>
                            <input type="text" class="form-control form-control-sm" name="username" placeholder="Leave blank for auto">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Password (Defaults to teacher123)</label>
                            <input type="password" class="form-control form-control-sm" name="password" placeholder="Defaults to teacher123">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/teachers') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Save Teacher
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
