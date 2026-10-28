<?php
use App\Helpers\Url;
use App\Helpers\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card card-custom p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon primary">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Create User Account</h4>
                        <p class="text-muted small mb-0">Add system administrator, teacher, or student login</p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to('/admin/users') ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-dark">Password (Min 6 chars) <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="role" class="form-label small fw-semibold text-dark">Role Authorization <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/users') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
