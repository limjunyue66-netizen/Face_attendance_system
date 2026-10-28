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
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-0">Edit User Account</h4>
                        <p class="text-muted small mb-0">Update credentials for <?= htmlspecialchars($user['username']) ?></p>
                    </div>
                </div>
                <a href="<?= Url::to('/admin/users') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <form action="<?= Url::to("/admin/users/{$user['id']}") ?>" method="POST">
                <?= Csrf::field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label small fw-semibold text-dark">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-dark">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-dark">Password (Leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" minlength="6">
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <label for="role" class="form-label small fw-semibold text-dark">Role Authorization <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="student" <?= ($user['role'] === 'student') ? 'selected' : '' ?>>Student</option>
                            <option value="teacher" <?= ($user['role'] === 'teacher') ? 'selected' : '' ?>>Teacher</option>
                            <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Administrator</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="status" class="form-label small fw-semibold text-dark">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= Url::to('/admin/users') ?>" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Update Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
